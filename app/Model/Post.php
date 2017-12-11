<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['name', 'body'];


    public function getAllPost() 
    {
      return $this->orderBy('created_at', 'desc')
                  ->get();
    }
 

    public function createPost($input)
    {
      return $this->create($input);
    }


    public function deletePost($id)
    {
      return $this->find($id)
                  ->delete();   
    }


    public function getPostFromId($id)
    {
      return $this->find($id);   
    }


    public function updatePost($input, $id)
    {
     return $this->find($id)
                   ->update($input);     
    }
    

}
