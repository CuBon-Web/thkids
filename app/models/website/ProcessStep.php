<?php

namespace App\models\website;

use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    protected $table = 'process_steps';
    protected $fillable = ['title', 'description', 'image', 'sort', 'status'];
}
