<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $fillable = ['name', 'description', 'semester_id', 'is_required', 'deadline'];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
