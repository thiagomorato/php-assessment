<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'content', 'sort_order' , 'done'
    ];


    /**
     * This function will get all tasks stored in the database, also it is
     * capable to sorting these results by any field and direction.
     *
     * @param String $sortBy    Field name, prepend + and - for directions.
     */
    public function getAll($sortBy = null)
    {
        if ($sortBy) {
            $direction = 'asc';

            if ($sortBy[0] === '-') {
                $direction = 'desc';
            }

            $field = [];
            preg_match("/\w+/", $sortBy, $field);
            $field = $field[0];

            if ('uuid') {
                $field = 'id';
            } else if ('date_created') {
                $field = 'created_at';
            }

            return $this->orderBy($field, $direction)->get();
        } else {
            return $this->all();
        }

        $this->orderBy($field, $direction)->get();
    }

}
