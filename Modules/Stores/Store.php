<?php

namespace Modules\Stores;


use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model {

	protected $table = 'stores';
	public $timestamps = true;

    use SoftDeletes;
    use SpatialTrait;

    protected $dates = ['deleted_at'];
    protected $hidden = ['id'];

    protected $fillable = [
        'user_id',
        'madeBy',
        'st_number',
        'vtour',
	    'title',
        'desc' ,
        'country_id',
        'state_id',
        'city_id',
        'phone',
        'address',
        'lat',
        'lng',
        'location',
        'siteRate',
        'status',
        'district'
    ];

    protected $spatialFields = [
        'location'
    ];

    protected $appends = ['fullVtour'];

    public function getFullVtourAttribute(){
        if($this->vtour) {
            return asset('uploads/admins/stores/vtours/'.$this->vtour.'/index.html');
        } else {
            return null;
        }
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'Y');
    }

    public function getRouteKeyName()
    {
        return 'st_number';
    }

    public function images()
    {
        return $this->hasMany( Images::class );
    }

    public function storeTypes()
    {
        return $this->belongsToMany(StoreType::class , 'store_store_types' , 'store_id' , 'type_id');
    }

    public function comments() {
        return $this->morphMany('Modules\Comments\Comment'  , 'commentable');
    }

    public function rates() {
        return $this->morphMany('Modules\Ratings\Rate'  , 'ratable');
    }

}