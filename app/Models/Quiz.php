<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $primaryKey = 'quiz_id';

    protected $fillable = [
        'content',
        'responses',
        'feedback',
    ];

    protected $casts = [
        'content' => 'array',
        'responses' => 'array',
    ];

    // العلاقة Many-to-Many مع الطلاب
    public function students()
    {
        return $this->belongsToMany(User::class, 'quiz_students', 'quiz_id', 'user_id');
    }
}
