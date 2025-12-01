<?php

namespace App\Models;

class Model {
    protected $table;
    protected $fillable = [];
    protected $attributes = [];
    
    public function __construct($attributes = []) {
        $this->attributes = $attributes;
    }
    
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }
    
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    public function toArray() {
        return $this->attributes;
    }
    
    public static function find($id) {
        $table = (new static)->table;
        $result = \DB::selectOne("SELECT * FROM {$table} WHERE id = ?", [$id]);
        return $result ? new static($result) : null;
    }
    
    public static function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        return new QueryBuilder((new static)->table, $column, $operator, $value);
    }
    
    public static function all() {
        $table = (new static)->table;
        $results = \DB::select("SELECT * FROM {$table}");
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }
    
    public function save() {
        $table = $this->table;
        $data = [];
        
        foreach ($this->fillable as $field) {
            if (isset($this->attributes[$field])) {
                $data[$field] = $this->attributes[$field];
            }
        }
        
        if (isset($this->attributes['id'])) {
            $set = [];
            $values = [];
            foreach ($data as $key => $value) {
                $set[] = "{$key} = ?";
                $values[] = $value;
            }
            $values[] = $this->attributes['id'];
            $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE id = ?";
            \DB::update($sql, $values);
        } else {
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $this->attributes['id'] = \DB::insert($sql, array_values($data));
        }
        return $this;
    }
    
    public function delete() {
        if (!isset($this->attributes['id'])) {
            return false;
        }
        $table = $this->table;
        return \DB::delete("DELETE FROM {$table} WHERE id = ?", [$this->attributes['id']]) > 0;
    }
    
    public static function create($data) {
        $model = new static($data);
        $model->save();
        return $model;
    }
}

class QueryBuilder {
    private $table;
    private $conditions = [];
    private $params = [];
    
    public function __construct($table, $column, $operator, $value) {
        $this->table = $table;
        $this->where($column, $operator, $value);
    }
    
    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->conditions[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }
    
    public function get() {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }
        $results = \DB::select($sql, $this->params);
        $modelClass = $this->getModelClass();
        return array_map(function($row) use ($modelClass) {
            return new $modelClass($row);
        }, $results);
    }
    
    public function first() {
        $results = $this->get();
        return $results[0] ?? null;
    }
    
    private function getModelClass() {
        $className = 'App\\Models\\' . ucfirst(rtrim($this->table, 's'));
        if (class_exists($className)) {
            return $className;
        }
        return 'App\\Models\\Model';
    }
}
