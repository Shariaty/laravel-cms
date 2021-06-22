<?php

namespace Modules\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Project extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'sort' , 'title', 'slug' , 'url' , 'body' , 'img' , 'is_published' , 'is_expired', 'views'
    ];

    protected $appends = [ 'cover_image' ];

    public function getCoverImageAttribute()
    {
        $final = null ;
        if($this->img) {
            $final =  asset('uploads/admins/project-pictures/'.$this->img);
        } else {
            $final =  asset('assets/admin/images/product-placeholder.jpg');
        }
        return $final;
    }

    public function categories()
    {
        return $this->belongsToMany( ProjectCategory::class , 'projects_projectscategories' , 'project_id' , 'category_id')->withTimestamps();
    }

}
