<?php

namespace App\Models;

class Category extends Model {
    protected $table = 'categories';
    
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 'order', 'is_active'
    ];
    
    public static function getActive() {
        $results = \DB::select("SELECT * FROM categories WHERE is_active = 1 ORDER BY `order` ASC");
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }
}
