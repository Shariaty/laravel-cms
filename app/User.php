<?php
namespace App;

use Modules\Comments\Comment;
use Modules\Stores\Store;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'status',
        'cell',
        'email',
        'fullName',
        'user_image',
        'last_login',
        'password',
        'activation_code'
    ];

    protected $appends = ['avatar'];

    protected $hidden = [
        'password','remember_token', 'activation_code'
    ];

    public function getAvatarAttribute()
    {
        $final = null;

        if($this->user_image) {
            $final = asset('uploads/admins/user-avatars/'.$this->user_image);
        } else {
            $final = asset('assets/admin/images/profile-placeholder.jpg');
        }

        return $final;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class , 'user_id' , 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class , 'sender_id' , 'id');
    }

    public function stores()
    {
        return $this->hasMany(Store::class , 'user_id' , 'id');
    }


}
