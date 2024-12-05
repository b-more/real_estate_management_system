<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'file_extension',
        'documentable_type',
        'documentable_id',
        'uploaded_by',
        'last_accessed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Get the parent documentable model.
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the full URL to download the document.
     */
    public function getDownloadUrlAttribute()
    {
        return '/storage/' . $this->file_path;
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        
        if ($bytes <= 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = floor(log($bytes, 1024));
        $value = round($bytes / pow(1024, $base), 2);
        
        return $value . ' ' . $units[$base];
    }

    /**
     * Update last accessed timestamp.
     */
    public function recordAccess(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Check if the document is an image.
     */
    public function isImage(): bool
    {
        return in_array(strtolower($this->file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Check if the document is a PDF.
     */
    public function isPdf(): bool
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($document) {
            // Set file size and extension when creating
            if ($document->file_path) {
                try {
                    $document->file_size = Storage::disk('public')->size($document->file_path);
                    $document->file_extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                } catch (\Exception $e) {
                    // Handle or log error if needed
                }
            }
            
            if (!$document->uploaded_by && auth()->check()) {
                $document->uploaded_by = auth()->id();
            }
        });
    
        static::deleted(function ($document) {
            Storage::disk('public')->delete($document->file_path);
        });
    }
}