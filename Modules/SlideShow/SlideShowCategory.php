<?php

namespace Modules\SlideShow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SlideShowCategory extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'slideshow_category';
    protected $fillable = [ 'title', 'slug' , 'desc' , 'is_published'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function slides()
    {
        return $this->hasMany( SlideShow::class , 'slideshow_category_id');
    }


}
