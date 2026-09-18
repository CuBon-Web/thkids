<?php

namespace App\models\website;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = ['question', 'answer', 'sort', 'status'];
}
