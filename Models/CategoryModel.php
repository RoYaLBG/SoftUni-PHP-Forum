<?php

namespace ANSR\Models;

/**
 * Category model
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class CategoryModel extends Model {
    
    public function getCategories() {
        $result = $this->getDb()->query("SELECT id, name, order_id FROM categories");
        
        return $this->getDb()->fetch($result);
    }

    public function getCategoryById($id) {
         $result = $this->getDb()->query("SELECT id, name, order_id FROM categories WHERE $id = " . intval($id));
        
        return $this->getDb()->row($result);
    }
    
    public function add($name) {
        $this->getDb()->query("INSERT INTO categories (name, order_id) VALUES ('$name', 1);");
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function edit($id, $name, $order_id = null) {
        $name = $this->getDb()->escape($name);
        $query = "UPDATE categories SET name = '$name' ";
        
        if ($order_id) {
            $query .= ", order_id = " . intval($order_id);
        }
        
        $query .= " WHERE id = " . intval($id);
        
        $this->getDb()->query($query);
        
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function delete($id) {
        $this->getDb()->query("DELETE FROM categories WHERE id = " . intval($id));
        return $this->getDb()->affectedRows() > 0;
    }
    
    
}