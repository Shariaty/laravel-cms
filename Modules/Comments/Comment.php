<?php

namespace Modules\Comments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Comment extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'comments';
    protected $fillable = ['body' , 'commentable_type' , 'sender_id' , 'status'];

    public function commentable() {
        return $this->morphTo();
    }

    public function scopeNotApproved($query)
    {
        return $query->where('status', COMMENT_TYPE_WAIT_TO_CONFIRM);
    }

}
