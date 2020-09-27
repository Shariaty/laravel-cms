<?php

namespace Modules\Portal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalTempRecord extends Model
{
    protected $table = 'portal_temp_records';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['admin_id' , 'sku' , 'eachStock', 'portal_id'];

    public function portal()
    {
        return $this->belongsTo(Portal::class , 'portal_id' , 'id');
    }

}
