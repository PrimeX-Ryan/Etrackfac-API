<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['faculty_id', 'requirement_id', 'file_path', 'status', 'remarks'];

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }
}
