<?php

namespace Modules\Magazines;

use App\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Magazine extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'magazines';

    protected $fillable = [
         'title', 'slug' , 'body' , 'img' , 'is_published' , 'file' , 'downloads' , 'fake'
    ];

    protected $appends = ['full_url_image'];

    public function scopeNotFake($query)
    {
        return $query->where('fake', '!=', 'Y');
    }

    public function categories()
    {
        return $this->belongsToMany( MagazineCategory::class , 'magazines_magazinescategories' , 'magazine_id' , 'category_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany('App\Admin\Comment', 'commentable')->where('approved' , 'Y');
    }

    public function commentsCount()
    {
        return $this->morphMany('App\Admin\Comment' , 'commentable')->selectRaw('commentable_id, count(*) as count')->groupBy('commentable_id');
    }

    public function admin(){
        return $this->belongsTo( Admin::class , 'admin_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getFullUrlImageAttribute()
    {
        $final = null;
        if($this->img) {
            $final = asset('uploads/admins/magazine-pictures/'.$this->img);
        }  else {
            $final = asset('assets/admin/images/magazine-placeholder.jpg');
        }
        return $final;
    }

}
