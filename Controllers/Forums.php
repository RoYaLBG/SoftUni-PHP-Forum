<?php

namespace ANSR\Controllers;

/**
 * Topics Controller
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class Forums extends Controller {
    
    public function view() {
        if ($this->getRequest()->getParam('id')) {
            $result = $this->getApp()->TopicModel->getTopicsByForumId($this->getRequest()->getParam('id'));
            
            $this->getView()->topics = $result;
            $this->getView()->forum = $this->getApp()->ForumModel->getForumById($this->getRequest()->getParam('id'));
            $this->getView()->forums = $this->getApp()->ForumModel->getForums();
        }
    }
    
    public function topics() {
        if ($this->getRequest()->getParam('id')) {
            $result = $this->getApp()->TopicModel->getTopicsByForumId($this->getRequest()->getParam('id'));
            
            $this->getView()->forum = $result;
        }
    }
    
    public function delete() {
        if (!$this->isAdmin) {
            die(json_encode(['success' => 0]));
        }
        
        if ($this->getRequest()->getParam('id')) {
            if (!$this->isCsrfTokenValid()) {
                die(json_encode(array('success' => 0)));
            }
            $id = $this->getRequest()->getParam('id');
            if ($this->getApp()->ForumModel->delete($id)) {
                die(json_encode(['success' => 1]));
            }
        }
        
        die(json_encode(['success' => 0]));
    }
}