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

    protected $appends = ['fullImage'];

    protected $fillable = [
        'sort' , 'cat_id' , 'title', 'percentage' , 'is_published' , 'file' , 'fake'
    ];

    public function scopeReal($query)
    {
        return $query->where('fake', 'N');
    }

    public function getFullImageAttribute()
    {
        $final = null;

        if($this->file) {
            $final = asset('uploads/admins/skill-pictures/'.$this->file);
        }

        return $final;
    }

    public function categories()
    {
        return $this->belongsTo(SkillCategory::class , 'cat_id');
    }

}
