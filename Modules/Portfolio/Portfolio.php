<?php

namespace Modules\Portfolio;

use App\Tag;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Portfolio extends Model {
    use Translatable;


    protected $table = 'portfolio';
	public $timestamps = true;

    use SoftDeletes;

    public $translatedAttributes = ['title' , 'desc' , 'meta'];

    protected $fillable = [
        'category_id',
        'designer_id',
        'fake',
        'type',
	    'year',
        'is_published',
        'file',
        'sheet'
    ];

    protected $appends = [  'visible_sku' , 'cover_image' ,
        'placeholder' , 'gallery' , 'gallery_count' , 'desc_escaped' , 'full_file' , 'full_sheet'];

    public function getFullFileAttribute()
    {
        $final = null ;
        if($this->file) {
            $final = asset('uploads/admins/portfolio/attachments').'/'.$this->file;
        }

        return $final;
    }

    public function getFullSheetAttribute()
    {
        $final = null ;
        if($this->sheet) {
            $final = asset('uploads/admins/portfolio/attachments').'/'.$this->sheet;
        }

        return $final;
    }


    public function getCoverImageAttribute()
    {
        $final = null ;
        if(count($this->images)) {
            $final =  $this->images[0]->full_url_image;
        } else {
            $final =  asset('assets/admin/images/product-placeholder.jpg');
        }
        return $final;
    }

    public function getDescEscapedAttribute()
    {
        return strip_tags($this->desc);
    }

    public function getVisibleSkuAttribute()
    {
        $finalSku = null;
        if($this->parent == null) {
            $finalSku = 'SKU-'.$this->sku;
        } else {
            $finalSku = 'SKU-'.$this->parent.'-'.$this->sku;
        }
        return $finalSku;
    }

    public function getPlaceholderAttribute()
    {
        return asset('assets/admin/images/product-placeholder.jpg');
    }

    public function getGalleryAttribute()
    {
        return $this->images;
    }

    public function getGalleryCountAttribute()
    {
        return $this->images->count();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', 'Y');
    }

    public function scopeNotFake($query)
    {
        return $query->where('fake', '!=' , 'Y');
    }

    public function category()
    {
        return $this->belongsTo( PortfolioCategory::class , 'category_id' ,'id');
    }

    public function images()
    {
        return $this->hasMany( Images::class );
    }

    public function designer()
    {
        return $this->belongsTo( Designer::class , 'designer_id' ,'id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }


}