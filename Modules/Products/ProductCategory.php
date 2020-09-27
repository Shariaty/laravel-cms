<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ProductCategory extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'products_categories';
    protected $fillable = [ 'parent' , 'title', 'slug' , 'desc' , 'img' , 'is_published' , 'has_age' , 'icon'];

    protected $appends = ['published_children'];

    //Slug engine
    public static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->slug = slug_utf8($model->title);// change the ToBeSluggiefied

            $latestSlug =
                static::whereRaw("slug = '$model->slug' or slug LIKE '$model->slug-%'")
                    ->latest('id')
                    ->value('slug');
            if ($latestSlug) {
                $pieces = explode('-', $latestSlug);

                $number = intval(end($pieces));

                $model->slug .= '-' . ($number + 1);
            }
        });
        static::updating(function($model) {
            $currentItemTitle = static::whereRaw("id = '$model->id'")
                ->latest('id')
                ->value('title');

            if(mb_strtolower($model->title) != mb_strtolower($currentItemTitle)) {

                $model->slug = mb_strtolower(slug_utf8($model->title));// change the ToBeSluggiefied

                $slugGenerated = mb_strtolower(slug_utf8($model->title));

                $latestSlug = static::where( function ($query) use ($model , $slugGenerated) {
                    $query->where('slug' , '=' , $slugGenerated);
                    $query->orWhere('slug', 'like', $slugGenerated.'%');
                })
                    ->pluck( 'slug' , 'id');

                $data =  array_except($latestSlug , $model->id);
                $sorted = array_values(array_sort($data, function ($value, $key) {
                    return $value;
                }));

                $lastSorted = last($sorted);
                if ($lastSorted) {
                    $pieces = explode('-', $latestSlug);
                    $number = intval(end($pieces));
                    $model->slug .= '-' . ($number + 1);
                }
            }
        });
    }
    //Slug engine

    public function getChildrenAttribute()
    {
        return $this->where('parent' , $this->id)->withCount('products')->get();
    }

    public function getPublishedChildrenAttribute()
    {
        return $this->where('parent' , $this->id)->published()->withCount('products')->get();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', '=', 'Y');
    }

    public function scopeParents($query)
    {
        return $query->where('parent', 0);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function products()
    {
        return $this->hasMany( Product::class ,'category_id',  'id' );
    }

    public function attributes(){
        return $this->belongsToMany( Attribute::class , 'attributes_product_categories' ,'category_id' , 'attribute_id' )
            ->withPivot('order')
            ->orderBy('pivot_order', 'asc');
    }

    public function options(){
        return $this->belongsToMany( Option::class , 'product_options_category' ,'category_id' , 'option_id' )->orderBy('option_id')->withTimestamps();
    }

    public function manufactures(){
        return $this->belongsToMany( Manufacture::class , 'manufacture_category' ,'category_id' , 'manufacture_id' );
    }

}
