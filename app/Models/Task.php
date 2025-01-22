<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

    protected $fillable = ['name','description','start_date','end_date','user_id','status'];

    /**
     * Creamos la relacion con el modelo User
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Creamos la relacion con el modelo TaskFile
     */
    public function files() {
        return $this->hasMany(TaskFile::class, 'task_id', 'id');
    }
}
