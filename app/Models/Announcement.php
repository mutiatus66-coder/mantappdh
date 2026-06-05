<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'content', 'publish_date', 'is_active'];

    protected $casts = [
        'publish_date' => 'date',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($announcement) {
            $announcement->slug = \Str::slug($announcement->title);
        });
    }
}
