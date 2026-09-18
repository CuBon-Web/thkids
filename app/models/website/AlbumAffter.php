<?php

namespace App\models\website;

use Illuminate\Database\Eloquent\Model;

class AlbumAffter extends Model
{
    protected $table = "albumafter";
    protected $fillable = ['before', 'after', 'title', 'status', 'sort'];
}
