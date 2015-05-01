<?php

namespace ANSR\Models;

/**
 * User model
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class UserModel extends Model {
    
    const ROLE_USER = 1;
    const ROLE_MODERATOR = 2;
    const ROLE_ADMINISTRATOR = 3;
    
    const INTERVAL_ONLINE_MINUTES = 15;
    
    const RANKING_TYPE_POSTS = 'posts';
    const RANKING_TYPE_UPVOTES = 'votes';
    
    const UPVOTE_COUNT = 1;
    const DOWNVOTE_COUNT = -1;

    public function register($username, $password, $email, $avatar = null, $role_id = self::ROLE_USER) {
        $username = $this->getDb()->escape($username);
        $password = md5($password);
        $email = $this->getDb()->escape($email);
        
        if ($this->userExists($username)) {
            return false;
        }
        
        $result = $this->getDb()->query("
            INSERT INTO 
                users 
            (username, password, email, avatar, role_id, register_date, last_click, last_page) 
                VALUES
            ('$username', '$password', '$email', '$avatar', '$role_id', NOW(), NOW(), 'users/register');
        ");
        
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function login($username, $password) {
        $username = $this->getDb()->escape($username);
        $password = md5($password);

        if ($this->userExists($username, $password)) {
            $_SESSION['user_id'] = $this->getIdByUsername($username);
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    }
    
    public function updateLastClick($user_id, $page) {
        $user_id = intval($user_id);
        
        $this->getDb()->query("UPDATE users SET last_click = NOW(), last_page = '$page' WHERE id = $user_id");
        
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function userExists($username, $password = null) {
        $username = $this->getDb()->escape($username);
        
        $query = "SELECT COUNT(*) AS cnt FROM users WHERE username = '$username' ";
        
        if ($password) {
            $query .= " AND password = '$password'";
        }
        
        $result = $this->getDb()->query($query);
        
        $row = $this->getDb()->row($result);
        
        return $row['cnt'] > 0;
    }
    
    public function getIdByUsername($username) {
        $username = $this->getDb()->escape($username);
        
        $result = $this->getDb()->query("SELECT id FROM users WHERE username = '$username';");
        
        $row = $this->getDb()->row($result);
        
        return isset($row['id']) ? $row['id'] : 0;
    }
    
    public function getUsernameById($id) {
        $id = intval($id);
        
        $result = $this->getDb()->query("SELECT username FROM users WHERE id = $id");
        
        $row = $this->getDb()->row($result);
        
        return isset($row['username']) ? $row['username'] : '';
    }
    
    public function getUserById($id) {
        $id = intval($id);
        
        $result = $this->getDb()->query("
            SELECT u.id, u.username, u.email, u.avatar, u.role_id, u.register_date, u.votes, COUNT(t.id) + COUNT(a.id) AS posts 
            FROM users u
            LEFT JOIN topics t ON t.user_id = u.id
            LEFT JOIN answers a ON a.user_id = u.id
            WHERE u.id = $id");
        
        $row = $this->getDb()->row($result);
        
        return !empty($row) ? $row : '';
    }

    public function getRole($user_id) {
        $user_id = intval($user_id);

        $result = $this->getDb()->query("SELECT role_id FROM users WHERE id = '$user_id';");

        $row = $this->getDb()->row($result);

        return !empty($row) ? $row['role_id'] : 'Guest';
    }
   
    public function isAdmin($user_id) {
        return $this->getRole($user_id) == self::ROLE_ADMINISTRATOR;
    }
    
    public function isModerator($user_id) {
        return $this->getRole($user_id) == self::ROLE_MODERATOR;
    }
    
    public function isLogged() {
        return isset($_SESSION['user_id']);
    }
    
    public function getLastRegisteredUser() {
        $result = $this->getDb()->query("SELECT id, username, email, avatar, role_id, register_date FROM users ORDER BY register_date DESC LIMIT 1");
        
        $row = $this->getDb()->row($result);
        
        return !empty($row) ? $row : ['id' => 0, 'username' => 'Np user'];
    }
    
    public function getOnlineUsers() {
        $result = $this->getDb()->query("SELECT id, username, last_click, last_page FROM users WHERE last_click > DATE_SUB(NOW(), INTERVAL " . self::INTERVAL_ONLINE_MINUTES . " MINUTE);");
        
        $rows = $this->getDb()->fetch($result);
        
        foreach ($rows as &$row) {
            switch ($row['last_page']):
                case 'Welcome/index':
                    $row['page'] = 'Viewing index';
                    break;
                case 'Froums/view':
                    $row['page'] = 'Viewing a forum';
                    break;
                case 'Topics/view':
                case 'Topics/all':
                    $row['page'] = 'Reading a topic';
                    break;
                case 'Topics/add':
                    $row['page'] = 'Writing a topic';
                    break;
                case 'Answers/add':
                    $row['page'] = 'Answering to a topic';
                    break;
                case 'Users/online':
                    $row['page'] = 'Reviewing who is online';
                    break;
                default:
                    $row['page'] = $row['last_page'];
                    break;
            endswitch;
            
            $params = explode('/', $row['last_page']);
            $row['controller'] = $params[0];
            $row['action'] = $params[1];
        }
        
        return $rows;
    }
    
    public function getUsers($ranking = null) {
        $query = "
            SELECT 
                users.id, users.username, email, role_id, votes, (COUNT(answers.id) + COUNT(topics.id)) AS posts, register_date
            FROM
                users
            LEFT JOIN
                answers
            ON
                users.id = answers.user_id
            LEFT JOIN
                topics
            ON
                users.id = topics.user_id
            GROUP BY
                users.id    
        ";
        
        switch ($ranking):
            case self::RANKING_TYPE_POSTS:
                $query .= " ORDER BY posts DESC";
                break;
            case self::RANKING_TYPE_UPVOTES:
                $query .= " ORDER BY votes DESC";
            default:
                break;
        endswitch;

        $result = $this->getDb()->query($query);
        
        return $this->getDb()->fetch($result);
    }
    
    public function hasVoted($voter_id, $voted_id, $vote, $topic_id = null, $answer_id = null) {
        $voter_id = intval($voter_id);
        $voted_id = intval($voted_id);
        
        $query = "
            SELECT 
                COUNT(*) AS cnt 
            FROM 
                user_votes 
            WHERE 
                voter_id = $voter_id AND 
                voted_id = $voted_id AND
                vote = $vote ";
        
        if ($topic_id) {
            if (!$this->getApp()->TopicModel->isAuthor($voted_id, $topic_id)) {
                return true;
            }
            
            $query .= " AND topic_id = " . intval($topic_id);
        } else if ($answer_id) {
            if (!$this->getApp()->AnswerModel->isAuthor($voted_id, $answer_id)) {
                return true;
            }
            
            $query .= " AND answer_id = " . intval($answer_id);
        } else {
            return true;
        }
        
        $result = $this->getDb()->query($query);
        
        $row = $this->getDb()->row($result);
        
        if (empty($row)) {
            return false;
        }

        return $row['cnt'] > 0;
    }
    
    private function vote($voter_id, $voted_id, $vote, $topic_id = null, $answer_id = null) {
        $voter_id = intval($voter_id);
        $voted_id = intval($voted_id);
        
        if ($voter_id == $voted_id) {
            return false;
        }
        
        if ($topic_id) {
            $topic_id = intval($topic_id);
        } else if ($answer_id) {
            $answer_id = intval($answer_id);
        } else {
            return false;
        }
        
        if (!in_array($vote, array(-1, 1))) {
            return false;
        }
        
        if ($this->hasVoted($voter_id, $voted_id, $vote, $topic_id, $answer_id)) {
            return false;
        }
               
        $query = "
            INSERT INTO
                user_votes
                (voter_id, voted_id, topic_id, answer_id, vote)
            VALUES
                ($voter_id, $voted_id, '$topic_id', '$answer_id', $vote)
        ";

        $this->getDb()->query($query);
        
        if ($this->getDb()->affectedRows() > 0) {
            $this->getDb()->query("UPDATE users SET votes = votes + $vote WHERE id = $voted_id");
        }
        
        return $this->getDb()->affectedRows() > 0;
    }
    
    public function upvote($voter_id, $voted_id, $topic_id, $answer_id) {
        return $this->vote($voter_id, $voted_id, self::UPVOTE_COUNT, $topic_id, $answer_id);
    }
    
    public function downvote($voter_id, $voted_id, $topic_id, $answer_id) {
        return $this->vote($voter_id, $voted_id, self::DOWNVOTE_COUNT, $topic_id, $answer_id);
    }
    
    public function edit($id, $username, $email, $password) {
        $id = intval($id);
        $username = $this->getDb()->escape($username);
        $email = $this->getDb()->escape($email);
        
        $query = "UPDATE users SET email = '$email', username = '$username' ";
        
        if (!empty($password)){
            $password = md5($password);
            $query .= " , password = '$password' ";
        }
        
        $query .= " WHERE id = $id";
        
        
        $this->getDb()->query($query);
        
        return $this->getDb()->affectedRows() > 0;
    } 
}

