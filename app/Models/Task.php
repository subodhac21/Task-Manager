<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'image', 'status_id', 'priority', 'created_by', 'assigned_to', 'assigned_at', 'completed_at'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }
}
