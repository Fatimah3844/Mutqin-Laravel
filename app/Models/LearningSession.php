<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningSession extends Model
{
    protected $fillable = ['sheikh_id', 'start_time', 'end_time', 'status'];

    public function sheikh()
    {
        return $this->belongsTo(User::class, 'sheikh_id');
    }

    public function students()
    {
         return $this->belongsToMany(User::class, 'session_student', 'session_id', 'student_id')
                ->withPivot('attended')
                ->withTimestamps();
    }
}
