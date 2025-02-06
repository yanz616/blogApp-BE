<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    //relasi many to many: tag dapat digunakan di banyak post
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'posts_tags')->withTimestamps();
    }
}
