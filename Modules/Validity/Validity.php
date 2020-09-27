<?php

namespace Modules\Validity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Validity extends Model
{
    protected $table = 'validation_records';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['admin_id' , 'identification' , 'date' , 'title' ,'is_published'];
}
