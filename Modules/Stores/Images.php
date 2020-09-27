<?php

namespace Modules\Stores;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Images extends Model {

	protected $table = 'store_images';
	public $timestamps = true;

    protected $fillable = [
	    'img',
        'size'
    ];

    protected $appends = ['fullImage'];

    public function getFullImageAttribute(){
        if($this->img) {
            return asset('uploads/admins/stores/images/'.$this->img );
        } else {
            return null;
        }
    }


}