<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MessContact extends Model
{
    protected $table = "mess_contact";
    protected $fillable = [
        'name', 'email', 'phone', 'mess',
        'service_id', 'service_name', 'service_slug', 'service_cate_slug',
    ];
}
