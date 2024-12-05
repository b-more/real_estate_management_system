<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plot_number',
        'size',
        'size_unit',
        'price',
        'length',
        'width',
        'location',
        'address',
        'amenities',
        'description',
        'legal_status',
        'status',
        'coordinates',
        'site_plan',
        'title_deed',
        'chief_letter',
    ];

    protected $casts = [
        'amenities' => 'array',
        'coordinates' => 'array',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'size' => 'decimal:2',
        'price' => 'decimal:2',
        'site_plan' => 'array',
        'title_deed' => 'array',
        'chief_letter' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plot) {
            // Automatically calculate size when creating
            if (isset($plot->length) && isset($plot->width)) {
                $plot->size = $plot->length * $plot->width;
                $plot->size_unit = $plot->size_unit ?? 'sq_m';
            }
        });

        static::updating(function ($plot) {
            // Recalculate size when length or width is updated
            if ($plot->isDirty(['length', 'width'])) {
                $plot->size = $plot->length * $plot->width;
            }
        });
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

}
