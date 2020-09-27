<?php

namespace Modules\Stores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreType extends Model {

	protected $table = 'store_types';
	public $timestamps = true;

    protected $fillable = [ 'id' ,  'title' ];

    public function stores()
    {
        return $this->belongsToMany( Store::class , 'store_store_types' , 'store_id' , 'type_id' );
    }

}