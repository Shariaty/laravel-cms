<?php

namespace Modules\SlideShow;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SlideShow extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Translatable;


    protected $table = 'slideshow';

    public $translatedAttributes = ['title' , 'desc'];

    protected $fillable = [
        'sort' , 'file', 'link' , 'is_published', 'fake' , 'slideshow_category_id'
    ];

    protected $appends = ['fullImage'];

    public function scopePublished($query)
    {
        return $query->where('fake', 'N');
    }

    public function getFullImageAttribute()
    {
        $final = null;

        if($this->file) {
            $final = asset('uploads/admins/slide-show-pictures/'.$this->file);
        }

        return $final;
    }

    public function category()
    {
        return $this->belongsTo( SlideShowCategory::class , 'slideshow_category_id');
    }

}
