<?php

namespace App;

use App\Admin\Blog;
use App\Admin\Page;
use App\Admin\Permission;
use App\Admin\Role;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_users';

    protected $fillable = [
        'img' , 'firstname', 'lastname' , 'email' , 'password' , 'cellphone' , 'status' , 'last_active'
    ];

    protected $hidden = [
        'password', 'status' ,'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        if(is_string($role)) {
            return $this->roles->contains('name' , $role);
        }

        return !! $role->intersect($this->roles)->count();
    }

    public function posts()
    {
        return $this->hasMany( Blog::class )->orderBy('id');
    }

    public function pages(){
        return $this->hasMany( Page::class , 'admin_user_id');
    }

    public function getStatusAttribute($value)
    {
        switch ($value)
        {
            case 0:
                return 'N';
            case 1:
                return 'Y';
            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] =  $value == 'Y' ? 1 : 0;
    }

}
