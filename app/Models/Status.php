<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['name'];

    public function tasks() {
        return $this->hasMany(Task::class);
    }

  
}
