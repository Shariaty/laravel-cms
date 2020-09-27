<?php

namespace Modules\Portal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portal extends Model
{
    protected $table = 'portal';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['admin_id' , 'created_at'];

    public function tempRecords()
    {
        return $this->belongsTo(PortalTempRecord::class , 'id' , 'portal_id');
    }

    public function records()
    {
        return $this->belongsTo(PortalRecord::class , 'id' , 'portal_id');
    }
}
