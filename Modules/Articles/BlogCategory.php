<?php

namespace Modules\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class BlogCategory extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'posts_categories';
    protected $fillable = [ 'title', 'slug' , 'desc' , 'is_published'];

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

    public function scopePublished($query)
    {
        return $query->where('is_published', '=', 'Y');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function posts()
    {
        return $this->belongsToMany( Blog::class , 'posts_postscategories' , 'category_id' ,  'post_id')->withTimestamps();
    }

}
