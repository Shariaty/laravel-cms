<?php

namespace Modules\Khadamat;


use Illuminate\Database\Eloquent\Model;

class ServicesTranslation extends Model {

    public $timestamps = false;

    protected $fillable = [
        'title', 'desc' , 'slug' , 'meta'
    ];


    //Slug engine
    public static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->slug = slug_utf8($model->title);

            $latestSlug = static::whereRaw("slug = '$model->slug' or slug LIKE '$model->slug-%'")->latest('id')->value('slug');
            if ($latestSlug) {
                $pieces = explode('-', $latestSlug);
                $number = intval(end($pieces));
                $model->slug .= '-' . ($number + 1);
            }
        });
        static::updating(function($model) {
            $currentItemTitle = static::whereRaw("id = '$model->id'")->latest('id')->value('title');

            if(mb_strtolower($model->title) != mb_strtolower($currentItemTitle)) {

                $model->slug = mb_strtolower(slug_utf8($model->title));

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

}