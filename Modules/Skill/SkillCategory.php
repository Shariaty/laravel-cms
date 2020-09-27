<?php

namespace Modules\Skill;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SkillCategory extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'skills_categories';
    protected $fillable = [ 'title', 'slug' , 'desc' , 'is_published'];

    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function skills()
    {
        return $this->hasMany( Skill::class , 'cat_id');
    }


}
