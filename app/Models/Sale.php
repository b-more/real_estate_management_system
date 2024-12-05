<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

enum SaleStatus: string 
{
    case Negotiation = 'negotiation';
    case Agreement = 'agreement';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}


class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plot_id',
        'customer_id',
        'agent_id',
        'sale_price',
        'status',
        'sale_date',
        'notes'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'status' => SaleStatus::class
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}