<?php

namespace Modules\Sale;

use App\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceList extends Model {

    protected $table = 'price_list';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [ 'code' , 'user_id' ];

    public function items()
    {
        return $this->hasMany( PriceListItem::class , 'price_list_id' , 'id');
    }

    public function user()
    {
        return $this->belongsTo(Admin::class , 'user_id' , 'id');
    }

}