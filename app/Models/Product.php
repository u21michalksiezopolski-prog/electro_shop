<?php

namespace App\Models;

class Product extends Model {
    protected $table = 'products';
    
    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'price', 
        'old_price', 'stock', 'sku', 'image', 'images', 'category_id', 
        'brand', 'specifications', 'is_active', 'is_featured', 'views'
    ];
    
    public function getCategory() {
        if ($this->category_id) {
            return Category::find($this->category_id);
        }
        return null;
    }
    
    public function getDiscountPercentage() {
        if ($this->old_price && $this->old_price > $this->price) {
            return round((($this->old_price - $this->price) / $this->old_price) * 100);
        }
        return 0;
    }
    
    public function isInStock() {
        return $this->stock > 0;
    }
    
    public static function findBySlug($slug) {
        $result = \DB::selectOne("SELECT * FROM products WHERE slug = ?", [$slug]);
        return $result ? new static($result) : null;
    }
    
    public static function getActive($filters = []) {
        $sql = "SELECT * FROM products WHERE is_active = 1";
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['brand'])) {
            $sql .= " AND brand = ?";
            $params[] = $filters['brand'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = $filters['max_price'];
        }
        
        $orderBy = $filters['sort'] ?? 'created_at DESC';
        switch ($orderBy) {
            case 'price_asc':
                $sql .= " ORDER BY price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY price DESC";
                break;
            case 'name':
                $sql .= " ORDER BY name ASC";
                break;
            default:
                $sql .= " ORDER BY created_at DESC";
        }
        
        $limit = $filters['limit'] ?? 12;
        $offset = ($filters['page'] ?? 1 - 1) * $limit;
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
        
        $results = \DB::select($sql, $params);
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }
}
