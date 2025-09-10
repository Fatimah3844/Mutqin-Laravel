<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $fillable = ['student_id', 'sessions_attended', 'quizzes_completed', 'pages_learned'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
