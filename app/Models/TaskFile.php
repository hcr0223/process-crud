<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TaskFile extends Model
{
    protected $fillable = ['task_id', 'path', 'filename'];

    /**
     * Seteamos un formato para el campo 'path'
     */
    protected function path(): Attribute {
        return Attribute::make(
            get: fn (string $value) => Storage::url($value),
        );
    }
}
