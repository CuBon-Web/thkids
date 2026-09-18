<?php

namespace App\models\website;

use Illuminate\Database\Eloquent\Model;

class WhyChoose extends Model
{
    protected $table = 'why_chooses';
    protected $fillable = ['title', 'description', 'image', 'link', 'sort', 'status'];
}
