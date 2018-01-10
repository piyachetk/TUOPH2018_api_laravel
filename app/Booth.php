<?php
/**
 * Created by PhpStorm.
 * User: Piyachet Kanda
 * Date: 11/14/2017
 * Time: 4:24 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Booth extends Model
{
    protected $fillable = ['id', 'name', 'description', 'type', 'preview', 'picture', 'location', 'time', 'points', 'admin', 'scanCount', 'tags', 'isHighlight'];
    protected $table = 'booths';

    protected $casts = [
        'admin' => 'array',
        'tags' => 'array',
        'isHighlight' => 'boolean'
    ];

    public $timestamps = true;

    protected $hidden = ['points', 'admin', 'scanCount'];
}