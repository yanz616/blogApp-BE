<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id'];

    //relasi many to many(inverse): like milik satu user dan setiap like dibuat untuk satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //relasi many to many(inverse): like milik satu post dan setiap like terkait dengan satu post
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
