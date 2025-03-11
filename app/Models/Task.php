<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'status', 'order'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
