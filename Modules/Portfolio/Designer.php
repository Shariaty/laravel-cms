<?php

namespace Modules\Portfolio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Designer extends Model
{
    use Notifiable;

    protected $table = 'portfolio_designer';
    protected $fillable = [ 'is_published' , 'fake' , 'title', 'slug' , 'desc' , 'img' ];

    protected $appends = ['full_url_image'];

    public function scopeNotFake($query)
    {
        return $query->where('fake', '!=', 'Y');
    }

    public function getFullUrlImageAttribute()
    {
        $final = null;
        if($this->img) {
            $final = asset('uploads/admins/designer-pictures/'.$this->img);
        }  else {
            $final = asset('assets/admin/images/magazine-placeholder.jpg');
        }
        return $final;
    }

    public function portfolio()
    {
        return $this->hasMany( Portfolio::class , 'designer_id' ,'id' );
    }

}
