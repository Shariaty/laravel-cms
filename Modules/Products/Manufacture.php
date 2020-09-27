<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Manufacture extends Model
{
    use Notifiable;

    protected $table = 'manufacture';
    protected $fillable = [ 'title', 'e_title' , 'image' ];

    protected $appends = ['full_url_image'];

    public function getFullUrlImageAttribute()
    {
        $final = null;

        if($this->image) {
            $final =  asset('uploads/admins/products/manufacture/'.$this->image);
        } else {
            $final =  asset('uploads/admins/products/manufacture/placeholder.jpg');
        }

        return $final ;
    }


    public function products()
    {
        return $this->hasMany( Product::class , 'manufacture_id' ,'id' );
    }

}
