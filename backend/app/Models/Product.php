<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'isbn',
        'author',
        'publisher',
        'publish_date',
        'pages',
        'description',
        'price',
        'stock',
        'cover_url',
        'status',
    ];
}
