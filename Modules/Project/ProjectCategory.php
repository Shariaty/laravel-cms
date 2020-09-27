<?php

namespace Modules\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ProjectCategory extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'projects_categories';
    protected $fillable = [ 'title', 'slug' , 'desc' , 'is_published'];

    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function projects()
    {
        return $this->belongsToMany( Project::class , 'projects_projectscategories' , 'category_id' ,  'project_id')->withTimestamps();
    }


}
