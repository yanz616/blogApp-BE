<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'title', 'content', 'image'];

    //relasi one to many(inverse): post milik satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //relasi one to many(inverse): post milik satu category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    //relasi one to many: post memiliki banyak comment
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    //relasi many to many: post memiliki banyak tag
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'posts_tags')->withTimestamps();
    }

    //relasi many to many: post bisa memiliki banyak like
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }
}
