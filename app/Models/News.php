<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    //
    protected $fillable = ['title', 'description', 'url', 'image', 'source', 'category', 'published_at'];

}
