<?php

namespace Modules\Khadamat;

use App\Tag;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Services extends Model {

    use Translatable;

	protected $table = 'services';
	public $timestamps = true;

    use SoftDeletes;

    public $translatedAttributes = ['title' , 'desc' , 'slug'];

    protected $fillable = [
        'fake',
        'parent',
        'is_published',
        'file',
        'sheet',
        'img'
    ];

    protected $appends = [ 'placeholder' , 'cover_image' , 'full_file' , 'full_sheet' , 'children' , 'full_parent' , 'full_url_image'];


    public function getFullUrlImageAttribute()
    {
        $final = null;
        if($this->img) {
            $final = asset('uploads/admins/general-images/'.$this->img);
        }  else {
            $final = asset('assets/admin/images/no-logo.jpg');
        }
        return $final;
    }

    public function getFullFileAttribute()
    {
        $final = null ;
        if($this->file) {
            $final = asset('uploads/admins/general-attachments').'/'.$this->file;
        }

        return $final;
    }

    public function getFullSheetAttribute()
    {
        $final = null ;
        if($this->sheet) {
            $final = asset('uploads/admins/general-attachments').'/'.$this->sheet;
        }

        return $final;
    }

    public function getPlaceholderAttribute()
    {
        return asset('assets/admin/images/product-placeholder.jpg');
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

    public function getChildrenAttribute()
    {
        return $this->where('parent' , $this->id)->get();
    }

    public function getFullParentAttribute()
    {
        $parent = null;
        if (isset($this->parent)) {
            $parent = $this->where('id' , $this->parent)->first();
        }
        return  $parent;
    }

    public function scopeParents($query)
    {
        return $query->where('parent', 0);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', 'Y');
    }

    public function scopeNotFake($query)
    {
        return $query->where('fake', '!=' , 'Y');
    }

    public function images()
    {
        return $this->hasMany( Images::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

}