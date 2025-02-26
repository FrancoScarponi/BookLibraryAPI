<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'image',
    ];

    public function books(){
        return $this->hasMany(Book::class);
    }
}
