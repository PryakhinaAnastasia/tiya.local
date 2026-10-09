<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'type', 'topic', 'difficulty', 'audio_file', 'image_file', 'image_file_2'];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
