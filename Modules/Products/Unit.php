<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Unit extends Model
{
    use Notifiable;

    protected $table = 'products_unit';
    protected $fillable = [ 'title', 'e_title' , 'conversion_factor'];

}
