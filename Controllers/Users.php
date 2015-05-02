<?php

namespace ANSR\Controllers;

/**
 * Users Controller
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class Users extends Controller {
    
    public function login() {
        
        if ($this->getRequest()->getPost()->getParam('username')) {
            if (!$this->isCsrfTokenValid()) {
                die(json_encode(array('success' => 0, 'msg' => 'Wrong CSRF Token')));
            }

            $user = $this->getRequest()->getPost()->getParam('username');
            $pass = $this->getRequest()->getPost()->getParam('password');
            
            if ($this->getApp()->UserModel->login($user, $pass)) {
                die(json_encode(array('success' => 1)));
            }
        }
        
        die(json_encode(array('success' => 0, 'msg' => 'Wrong or missing credentials')));
    }
    
    public function register() {
        
        if ($this->getRequest()->getPost()->getParam('username')) {
            $user = $this->getRequest()->getPost()->getParam('username');
            $pass = $this->getRequest()->getPost()->getParam('password');
            $email = $this->getRequest()->getPost()->getParam('email');
            
            if ($this->getApp()->UserModel->register($user, $pass, $email)) {
                $this->getApp()->UserModel->login($user, $pass);
                die(json_encode(array('success' => 1)));
            }
        }
        
        die(json_encode(array('success' => 0, 'msg' => 'User exists or missing credentials')));
    }
    
    public function online() {
        $this->getView()->users = $this->getApp()->UserModel->getOnlineUsers();
    }
    
    public function rankings() {
        
        $type = \ANSR\Models\UserModel::RANKING_TYPE_POSTS;
        
        if ($this->getRequest()->getParam('type')) {
            $type = $this->getRequest()->getParam('type');
        }
        
        $this->getView()->users = $this->getApp()->UserModel->getUsers($type);
    }
    
    public function profile() {
        if ($this->getRequest()->getParam('id')) {
            $id = $this->getRequest()->getParam('id');
            $user = $this->getApp()->UserModel->getUserById($id);
            
            switch ($user['role_id']):
                case \ANSR\Models\UserModel::ROLE_USER:
                    $user['role'] = 'User';
                    break;
                case \ANSR\Models\UserModel::ROLE_MODERATOR:
                    $user['role'] = 'Moderator';
                    break;
                case \ANSR\Models\UserModel::ROLE_ADMINISTRATOR:
                    $user['role'] = 'Administrator';
                    break;
                default:
                    $user['role'] = 'Guest';
                    break;
            endswitch;
            
            $this->getView()->user = $user;
            $this->getView()->isOwnProfile = (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $user['id']));
            $this->getView()->isAdmin = $this->isAdmin;
        }
    }

    public function isAdmin() {
        return $this->getApp()->UserModel->getRole($_SESSION['user_id']) == \ANSR\Models\UserModel::ROLE_ADMINISTRATOR;
    }

    public function isModerator() {
        return $this->getApp()->UserModel->getRole($_SESSION['user_id']) == \ANSR\Models\UserModel::ROLE_MODERATOR;
    }
    
    public function logout() {
        if (!$this->isCsrfTokenValid()) {
            die(json_encode(array('success' => 0, 'msg' => 'Wrong CSRF Token')));
        }
        session_destroy();
        exit;
    }
    
    /**
     * GET: id
     * POST: topicid || answerid
     * POST: action
     * 
     * users/vote/id
     */
    public function vote() {
        if (!$this->getApp()->UserModel->isLogged()) {
            die(json_encode(['success' => 0]));
        }
        
        if (false == ($voted_id = $this->getRequest()->getParam('id'))) {
            die(json_encode(['success' => 0]));
        }
        
        if ($this->getRequest()->getPost()->getParam('topicid')) {
            $answer_id = null;
            $topic_id = $this->getRequest()->getPost()->getParam('topicid');
        } else if ($this->getRequest()->getPost()->getParam('answerid')) {
            $topic_id = null;
            $answer_id =  $this->getRequest()->getPost()->getParam('answerid');
        } else {
            die(json_encode(['success' => 0]));
        }
        
        if (false == ($action = $this->getRequest()->getPost()->getParam('action'))) {
            die(json_encode(['success' => 0]));
        }
        
        $voter_id = $_SESSION['user_id'];
        
        if ($action == \ANSR\Models\UserModel::UPVOTE_COUNT) {
            if($this->getApp()->UserModel->upvote($voter_id, $voted_id, $topic_id, $answer_id)) {
                die(json_encode(['success' => 1]));
            }
        } elseif ($action == \ANSR\Models\UserModel::DOWNVOTE_COUNT) {
            if($this->getApp()->UserModel->downvote($voter_id, $voted_id, $topic_id, $answer_id)) {
                die(json_encode(['success' => 1]));
            }
        }
        
        die(json_encode(['success' => 0]));
    }
    
    public function edit() {
        if (!$this->getApp()->UserModel->isLogged()) {
            $this->redirect('welcome', 'index');
        }
        
        if ($this->getRequest()->getParam('id')) {
            $id = $this->getRequest()->getParam('id');
            if ($id == $_SESSION['user_id'] || $this->isAdmin) {
                if (!$this->isCsrfTokenValid()) {
                    $this->redirect('welcome');
                }
                $username = $this->getRequest()->getPost()->getParam('username');
                $email = $this->getRequest()->getPost()->getParam('email');
                $password = $this->getRequest()->getPost()->getParam('password');
                $passwordRepeat = $this->getRequest()->getPost()->getParam('passwordRepeat');
                
                if ($password != $passwordRepeat) {
                    $this->redirect('welcome', 'index');
                }

                if ($this->getApp()->UserModel->edit($id, $username, $email, $password)) {
                    $this->redirect('users', 'profile', 'id', $id);
                }
            }
        }
        
        $this->redirect('welcome', 'index');
    }

}

