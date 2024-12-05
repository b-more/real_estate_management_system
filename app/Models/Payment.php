<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'sale_id',
        'amount',
        'type',
        'status',
        'due_date',
        'payment_date',
        'receipt_number',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
