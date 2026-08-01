<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFinancialTransactionRequest;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\ProjectOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        // 1. Calculate Core Summary Cards
        $totalIncome = FinancialTransaction::where('type', 'income')->sum('amount');
        $totalExpense = FinancialTransaction::where('type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyIncome = FinancialTransaction::where('type', 'income')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $monthlyExpense = FinancialTransaction::where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        // Unpaid receivables from active project orders
        $unpaidReceivables = ProjectOrder::whereNotIn('status', ['cancelled'])
            ->whereColumn('budget', '>', 'paid_amount')
            ->selectRaw('SUM(budget - paid_amount) as total')
            ->value('total') ?? 0;

        // 2. Chart Data: Monthly Cashflow Trend (Last 6 Months)
        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $chartLabels[] = $date->translatedFormat('M Y');

            $inc = FinancialTransaction::where('type', 'income')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount');

            $exp = FinancialTransaction::where('type', 'expense')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount');

            $chartIncome[] = (int) $inc;
            $chartExpense[] = (int) $exp;
        }

        // 3. Category Distribution (Expenses)
        $expenseCategories = FinancialCategory::where('type', 'expense')
            ->withSum(['transactions' => function ($q) {
                $q->where('type', 'expense');
            }], 'amount')
            ->get()
            ->filter(fn ($cat) => ($cat->transactions_sum_amount ?? 0) > 0)
            ->values();

        // 4. Query Transactions Table with Filters
        $query = FinancialTransaction::with(['category', 'projectOrder', 'creator'])
            ->latest('transaction_date')
            ->latest('id');

        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('transaction_code', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->paginate(15)->withQueryString();
        $categories = FinancialCategory::orderBy('name')->get();

        return view('admin.finance.index', compact(
            'totalBalance',
            'totalIncome',
            'totalExpense',
            'monthlyIncome',
            'monthlyExpense',
            'unpaidReceivables',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'expenseCategories',
            'transactions',
            'categories'
        ));
    }

    public function store(StoreFinancialTransactionRequest $request)
    {
        $validated = $request->validated();
        $validated['transaction_code'] = FinancialTransaction::generateTransactionCode();
        $validated['created_by'] = Auth::id();

        FinancialTransaction::create($validated);

        return redirect()->route('admin.finance.index')
            ->with('success', 'Transaksi keuangan berhasil ditambahkan.');
    }

    public function destroy(FinancialTransaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('admin.finance.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'nullable|string|max:20',
        ]);

        FinancialCategory::create($request->only('name', 'type', 'color'));

        return redirect()->route('admin.finance.index')
            ->with('success', 'Kategori keuangan berhasil ditambahkan.');
    }

    public function destroyCategory(FinancialCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.finance.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
