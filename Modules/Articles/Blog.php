<?php

namespace Modules\Articles;

use App\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Blog extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
         'title', 'slug' , 'body' , 'img' , 'is_published' , 'views' , 'created_at'
    ];

    protected $appends = ['body_escaped' , 'full_img' , 'cyear'];

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

    public function getBodyEscapedAttribute()
    {
        return strip_tags($this->body);
    }

    public function getFullImgAttribute()
    {
        $result = null;
        if(!empty($this->img)){
            $result = asset('uploads/admins/article-pictures/'.$this->img)  ;
        } else {
            $result = asset('assets/admin/images/no-image.jpg');
        }
        return $result;
    }

    public function getCyearAttribute()
    {
        return $this->title;
    }


    public function categories()
    {
        return $this->belongsToMany( BlogCategory::class , 'posts_postscategories' , 'post_id' , 'category_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany('App\Admin\Comment', 'commentable')->where('approved' , 'Y');
    }

    public function commentsCount()
    {
        return $this->morphMany('App\Admin\Comment' , 'commentable')->selectRaw('commentable_id, count(*) as count')->groupBy('commentable_id');
    }

    public function admin(){
        return $this->belongsTo( Admin::class , 'admin_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
