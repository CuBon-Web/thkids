<?php

namespace App\models\website;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = "banners";
    protected $fillable = [
        'id',
        'image',
        'image_mobile',
        'type',
        'video_url',
        'status',
        'link',
        'title',
        'description',
    ];

    public function getYoutubeIdAttribute()
    {
        if ($this->type !== 'youtube' || empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        if (preg_match(
            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
            $url,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }

    public function isYoutube()
    {
        return $this->type === 'youtube' && $this->youtube_id;
    }
}
