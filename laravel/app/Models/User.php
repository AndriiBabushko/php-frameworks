<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    // Масив атрибутів, що можуть масово призначатись
    protected $fillable = [
        'username', 'email', 'password', 'profile_picture'
    ];

    // Відношення: один користувач має багато постів
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
