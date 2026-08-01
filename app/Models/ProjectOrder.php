<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class ProjectOrder extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'client_name',
        'client_contact',
        'title',
        'description',
        'budget',
        'paid_amount',
        'status',
        'priority',
        'start_date',
        'deadline',
        'order',
    ];

    protected $casts = [
        'budget' => 'integer',
        'paid_amount' => 'integer',
        'start_date' => 'date',
        'deadline' => 'date',
        'order' => 'integer',
    ];

    protected $appends = ['formatted_budget', 'formatted_paid', 'payment_progress'];

    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp ' . number_format($this->budget, 0, ',', '.');
    }

    public function getFormattedPaidAttribute(): string
    {
        return 'Rp ' . number_format($this->paid_amount, 0, ',', '.');
    }

    public function getPaymentProgressAttribute(): int
    {
        if ($this->budget <= 0) return 0;
        $progress = round(($this->paid_amount / $this->budget) * 100);
        return min(100, max(0, (int) $progress));
    }

    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'project_order_id');
    }
}
