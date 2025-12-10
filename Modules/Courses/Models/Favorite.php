<?php

namespace Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorites';

    protected $fillable = [
        'user_id',
        'course_id',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

