<?php

namespace App\Admin;

use App\Admin;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name' , 'label'];

    public function users()
    {
        return $this->belongsToMany(Admin::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
