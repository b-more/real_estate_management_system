<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'user_id',
        'type',
        'status',
        'notes',
        'interaction_date',
        'follow_up_date',
        'priority',
        'tags'
    ];

    protected $casts = [
        'interaction_date' => 'datetime',
        'follow_up_date' => 'datetime',
        'tags' => 'array'
    ];

    /**
     * Interaction types
     */
    const TYPES = [
        'inquiry' => 'General Inquiry',
        'viewing' => 'Property Viewing',
        'negotiation' => 'Price Negotiation',
        'follow_up' => 'Follow-up Call',
        'complaint' => 'Complaint',
        'documentation' => 'Documentation',
        'payment' => 'Payment Discussion',
        'other' => 'Other'
    ];

    /**
     * Interaction statuses
     */
    const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'requires_follow_up' => 'Requires Follow-up'
    ];

    /**
     * Priority levels
     */
    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    /**
     * Get the customer associated with the interaction.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user (staff member) associated with the interaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for overdue follow-ups
     */
    public function scopeOverdueFollowUps($query)
    {
        return $query
            ->where('status', 'requires_follow_up')
            ->whereDate('follow_up_date', '<=', now());
    }

    /**
     * Scope for interactions requiring follow-up
     */
    public function scopeRequiresFollowUp($query)
    {
        return $query->where('status', 'requires_follow_up');
    }

    /**
     * Scope for interactions within a date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query
            ->when($startDate, fn($q) => $q->whereDate('interaction_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('interaction_date', '<=', $endDate));
    }
}