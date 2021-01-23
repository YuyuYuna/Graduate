<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentClass extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'department_classes';

    public function students()
    {
        return $this->hasMany('App\Models\User', 'class_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }
}
