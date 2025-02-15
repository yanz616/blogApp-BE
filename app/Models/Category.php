<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Post;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relasi One-to-Many: Kategori memiliki banyak Post
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
