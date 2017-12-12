<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class Comment extends Model
{
  protected $fillable = ['body', 'post_id'];
  
  public function post() 
  {
    return $this->belongsTo('App\Model\Post');
  }


  public function createComment($input)
  {
    return $this->create($input);
  }

  
  public function deleteComment($comment)
  { 
   return $this->find($comment)
                ->delete();
  }



}
