<?php

namespace Modules\Sale;

use App\Address;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Warehouse\WarehouseOutGo;

class SaleInvoice extends Model {

    protected $table = 'sale_invoice';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [ 'user_id' , 'status' , 'type' , 'title' , 'invoice_number' ,
                            'total_price' , 'total_qty' , 'discount' , 'shipping' , 'address_id' ,
                            'is_paid' , 'transaction_id'];


    public function scopeSiteOrders($query)
    {
        return $query->where('type', SITE_ORDER);
    }

    public function goods()
    {
        return $this->hasMany( Goods::class , 'sale_invoice_id' , 'id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class , 'address_id' , 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id' , 'id');
    }

    public function wareHouseOutGo()
    {
        return $this->hasOne(WarehouseOutGo::class , 'sale_invoice_id' , 'id');
    }

}