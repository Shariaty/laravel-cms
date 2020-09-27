<?php

namespace Modules\SlideShow;


use Illuminate\Database\Eloquent\Model;

class SlideShowTranslation extends Model {

    public $timestamps = false;

    protected $fillable = [
        'title', 'desc'
    ];
}