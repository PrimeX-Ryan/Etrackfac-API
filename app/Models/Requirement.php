<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $fillable = ['name', 'description', 'semester', 'is_required'];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
