<?php

namespace ANSR\Controllers;

/**
 * Answers Controller
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class Answers extends Controller {
    
    public function add() {
        $username = null;
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        if (!$this->getApp()->UserModel->isLogged()) {
            $username = $this->getRequest()->getPost()->getParam('username');
            if (!$username) {
                die(['success' => 0]);
            }
        }
        
        $body = $this->getRequest()->getPost()->getParam('body');
        $topic_id = $this->getRequest()->getParam('topicid');
        
        if ($this->getApp()->AnswerModel->add($body, $topic_id, $user_id, $username)) {
            die(json_encode(['success' => 1]));
        }
        
        die(json_encode(['success' => 0]));
    }
    
    public function edit() {
        if ($this->getRequest()->getParam('id')) {
            $answer_id = $this->getRequest()->getParam('id');
            $answer = $this->getApp()->AnswerModel->getAnswerById($answer_id);

            $isOwnAnswer = (isset($_SESSION['user_id']) && $answer['user_id'] == $_SESSION['user_id']);
            
            $body = $this->getRequest()->getPost()->getParam('body');
            
            if ($isOwnAnswer || $this->isAdmin) {
                if ($this->getApp()->AnswerModel->edit($answer_id, $body)) {
                    die(json_encode(['success' => 1]));
                }
            }
        }
        
        die(json_encode(['success' => 0]));
    }
    
    public function delete() {
        if ($this->getRequest()->getParam('id')) {
            $answer_id = $this->getRequest()->getParam('id');
            $answer = $this->getApp()->AnswerModel->getAnswerById($answer_id);

            $isOwnAnswer = (isset($_SESSION['user_id']) && $answer['user_id'] == $_SESSION['user_id']);
           
            if ($isOwnAnswer || $this->isAdmin) {
                if ($this->getApp()->AnswerModel->delete($answer_id)) {
                    die(json_encode(['success' => 1]));
                }
            }
        }
        
        die(json_encode(['success' => 0]));
    }
}