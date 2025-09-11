<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['username', 'email', 'password', 'role', 'google_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==================relations=================

    public function sessions()
    {
        return $this->hasMany(LearningSession::class, 'sheikh_id'); 
    }

    public function studentSessions()
    {
         return $this->belongsToMany(LearningSession::class, 'session_student', 'student_id', 'session_id')
                ->withPivot('attended')
                ->withTimestamps();
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'badge_user');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function pagesLearned()
    {
        return $this->hasMany(PageLearned::class);
    }

    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }

    public function quizzes()
{
    return $this->belongsToMany(Quiz::class, 'quiz_students', 'user_id', 'quiz_id');
}

}
