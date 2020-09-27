<?php

namespace Modules\Products;


use Illuminate\Database\Eloquent\Model;

class Images extends Model {

	protected $table = 'product_images';
	public $timestamps = true;

    protected $fillable = [
	    'img',
        'size'
    ];

    protected $appends = ['full_url_image'];

    public function getFullUrlImageAttribute()
    {
        return asset('uploads/admins/products/images/'.$this->img);
    }


}