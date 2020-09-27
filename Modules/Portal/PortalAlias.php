<?php

namespace Modules\Portal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalAlias extends Model
{
    protected $table = 'portal_alias_records';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['admin_id' , 'sku' , 'portal_id' , 'is_published'];
}
