<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'date_of_birth',
        'nationality',
        'id_type',
        'id_number',
        'occupation',
        'company_name',
        'type', // individual or corporate
        'status', // active, inactive, blocked
        'source', // referral, website, agent, etc.
        'notes',
        'preferences',
        'tags',
        'credit_limit',
        'total_purchases',
        'last_purchase_date',
        'assigned_agent_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'preferences' => AsCollection::class,
        'tags' => 'array',
        'credit_limit' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'last_purchase_date' => 'datetime',
    ];

    // Relationships
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim("{$this->title} {$this->first_name} {$this->last_name}");
    }

    // Query Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCorporate($query)
    {
        return $query->where('type', 'corporate');
    }

    public function scopeIndividual($query)
    {
        return $query->where('type', 'individual');
    }
}