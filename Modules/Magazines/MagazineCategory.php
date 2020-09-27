<?php

namespace Modules\Magazines;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MagazineCategory extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'magazines_categories';
    protected $fillable = [ 'title', 'slug' , 'desc' , 'is_published'];

    public function scopePublished($query)
    {
        return $query->where('is_published', '=', 'Y');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function magazines()
    {
        return $this->belongsToMany( Magazine::class , 'magazines_magazinescategories' , 'category_id' ,  'magazine_id')->withTimestamps();
    }

}
