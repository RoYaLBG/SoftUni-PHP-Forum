<?php

namespace ANSR\Models;

/**
 * Forum model
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class ForumModel extends Model {
    
    public function getForumsByCategoryId($category_id) {
        $result = $this->getDb()->query("SELECT id, name, category_id, order_id FROM forums WHERE category_id = " . intval($category_id));
        
        return $this->getDb()->fetch($result);
    }

    public function getForumById($id) {
        $result = $this->getDb()->query("SELECT id, name, category_id, order_id FROM forums WHERE id = " . intval($id));
        
        return $this->getDb()->row($result);
    }
    
    public function getForums() {
        $result = $this->getDb()->query("SELECT id, name, category_id, order_id FROM forums");
        
        return $this->getDb()->fetch($result);
    }
    
    public function add($name, $category_id, $order_id) {
        $name = $this->getDb()->escape($name);
        $category_id = intval($category_id);
        $order_id = intval($order_id);
        $this->getDb()->query("INSERT INTO forums (name, category_id, order_id) VALUES ('$name', $category_id, $order_id)");
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function edit($id, $name, $category_id = null, $order_id = null) {
        $name = $this->getDb()->escape($name);
        $query = "UPDATE forums SET name = '$name' ";
        
        if ($category_id) {
            $query .= " , category_id = " . intval($category_id);
        }
        
        if ($order_id) {
            $query .= " , order_id = " . intval($order_id);
        }
        
        $query .= " WHERE id = " . intval($id);
        
        $this->getDb()->query($query);
        
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function delete($id) {
        $this->getDb()->query("DELETE FROM forums WHERE id = " . intval($id));
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function getPostsCount($forum_id) {
        $forum_id = intval($forum_id);

        $result = $this->getDb()->query("SELECT COUNT(*) AS cnt FROM forums f INNER JOIN topics t ON f.id = t.forum_id INNER JOIN answers a ON a.topic_id = t.id WHERE f.id = $forum_id");
    
        $row = $this->getDb()->row($result);
        
        return isset($row['cnt']) ? $row['cnt'] : 0;
    }
    
    public function getTopicsCount($forum_id) {
        $forum_id = intval($forum_id);
        
        $result = $this->getDb()->query("SELECT COUNT(*) AS cnt FROM forums f INNER JOIN topics t ON f.id = t.forum_id WHERE f.id = $forum_id");
    
        $row = $this->getDb()->row($result);

        return isset($row['cnt']) ? $row['cnt'] : 0;
    }
    
    public function getLastAuthorInfo($forum_id) {
        $forum_id = intval($forum_id);
        $result = $this->getDb()->query("
            SELECT 
                IF(u.username IS NULL, CONCAT(a.username, ' (Guest)'), u.username) AS username, a.created_on 
            FROM 
                forums f 
            INNER JOIN 
                topics t 
            ON 
                f.id = t.forum_id 
            INNER JOIN 
                answers a
            ON 
                a.topic_id = t.id
            LEFT JOIN
                users u
            ON 
                a.user_id = u.id
            WHERE 
                f.id = $forum_id
            ORDER BY
                created_on DESC
            LIMIT 1"
        );

        $row = $this->getDb()->row($result);
        
        return !empty($row) ? $row : array('username' => 'No author', 'created_on' => '');
    }
}