<?php

namespace App;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Chats extends Model
{
    //
    use Searchable;
    protected $fillable = ['title', 'details','user_id','status','chat_id'];

    //defining the relationship
    public function user()
    {
        return $this->belongsTo('App\User');
    }
     /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $name = "";
        if(!empty($this->user)) {
        	$name =  $this->user->name; 
    	}
    	
        $array = $this->toArray();
        $array = $this->transform($array);

        // Add an extra attribute:
        $array['added_month'] = substr($array['created_at'], 0, 7);
        $array['user_name'] = $name;
    	//$array['user_email'] = $this->user->email;
        return $array;
    }
}
