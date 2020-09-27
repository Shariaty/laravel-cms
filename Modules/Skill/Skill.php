<?php

namespace Modules\Skill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Skill extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'skills';

    protected $fillable = [
        'sort' , 'cat_id' , 'title', 'percentage' , 'is_published'
    ];

    public function categories()
    {
        return $this->belongsTo(SkillCategory::class , 'cat_id');
    }

}
