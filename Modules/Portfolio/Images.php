<?php

namespace Modules\Portfolio;


use Illuminate\Database\Eloquent\Model;

class Images extends Model {

	protected $table = 'portfolio_images';
	public $timestamps = true;

    protected $fillable = [
	    'img',
        'size'
    ];

    protected $appends = ['full_url_image'];

    public function getFullUrlImageAttribute()
    {
        return asset('uploads/admins/portfolio/images/'.$this->img);
    }


}