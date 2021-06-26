<?php

namespace App\Admin;

use App\Tag;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Page extends Model
{
    use Translatable;

    use Notifiable;
    use SoftDeletes;

    protected $table = 'pages';
    public $translatedAttributes = ['title', 'slug' , 'meta' ,'desc'];

    protected $fillable = [
        'is_published' , 'views' , 'admin_user_id'
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

}
