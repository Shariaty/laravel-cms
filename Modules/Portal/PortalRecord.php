<?php

namespace Modules\Portal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalRecord extends Model
{
    protected $table = 'portal_records';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['admin_id' , 'sku' , 'stock', 'portal_id' , 'status'];

    public function scopeFailed($query)
    {
        return $query->where('status', 2);
    }

    public function scopeSucceed($query)
    {
        return $query->where('status', 1);
    }

    public function portal()
    {
        return $this->belongsTo(Portal::class , 'portal_id' , 'id');
    }

}
