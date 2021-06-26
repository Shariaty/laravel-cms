<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use softDeletes;
    protected  $dates=['deleted_at'];
    protected $table = 'tags';
    protected  $fillable=['tag_name'];

}
