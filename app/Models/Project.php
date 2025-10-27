<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function latestReport()
    {
        return $this->hasOne(Report::class)->latestOfMany('last_generated_at');
    }
}
