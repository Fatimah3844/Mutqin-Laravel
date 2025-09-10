<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageLearned extends Model
{
    protected $fillable = ['user_id', 'page_number', 'sura_name', 'learned_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
