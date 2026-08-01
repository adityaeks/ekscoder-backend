<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class FinancialTransaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'transaction_code',
        'type',
        'category_id',
        'project_order_id',
        'amount',
        'title',
        'notes',
        'transaction_date',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }

    public function projectOrder()
    {
        return $this->belongsTo(ProjectOrder::class, 'project_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateTransactionCode(): string
    {
        $prefix = 'TRX-' . date('Ymd') . '-';
        $latest = self::where('transaction_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $number = (int) substr($latest->transaction_code, -4) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
