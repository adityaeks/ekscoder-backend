<?php

namespace App\Observers;

use App\Models\ProjectOrder;
use App\Models\FinancialTransaction;
use App\Models\FinancialCategory;
use Illuminate\Support\Facades\Auth;

class ProjectOrderObserver
{
    /**
     * Handle the ProjectOrder "created" event.
     */
    public function created(ProjectOrder $projectOrder): void
    {
        if ($projectOrder->paid_amount > 0) {
            $this->createIncomeTransaction($projectOrder, $projectOrder->paid_amount);
        }
    }

    /**
     * Handle the ProjectOrder "updated" event.
     */
    public function updated(ProjectOrder $projectOrder): void
    {
        if ($projectOrder->isDirty('paid_amount')) {
            $oldPaid = (int) $projectOrder->getOriginal('paid_amount');
            $newPaid = (int) $projectOrder->paid_amount;
            $diff = $newPaid - $oldPaid;

            if ($diff > 0) {
                $this->createIncomeTransaction($projectOrder, $diff);
            }
        }
    }

    private function createIncomeTransaction(ProjectOrder $projectOrder, int $amount): void
    {
        $category = FinancialCategory::firstOrCreate(
            ['name' => 'Project Payment', 'type' => 'income'],
            ['color' => '#10b981']
        );

        FinancialTransaction::create([
            'transaction_code' => FinancialTransaction::generateTransactionCode(),
            'type' => 'income',
            'category_id' => $category->id,
            'project_order_id' => $projectOrder->id,
            'amount' => $amount,
            'title' => "Pembayaran Project: {$projectOrder->client_name} - {$projectOrder->title}",
            'notes' => "Otomatis dicatat dari update pembayaran Project Order #{$projectOrder->id}",
            'transaction_date' => now()->toDateString(),
            'created_by' => Auth::id(),
        ]);
    }
}
