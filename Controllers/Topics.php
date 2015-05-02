<?php

namespace ANSR\Controllers;

/**
 * Topics Controller
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class Topics extends Controller {

    public function all() {
        $topics = $this->getApp()->TopicModel->getTopics();

        $this->getView()->topics = $topics;
    }

    public function view() {
        if ($this->getRequest()->getParam('id')) {
            $topic = $this->getApp()->TopicModel->getTopicById($this->getRequest()->getParam('id'));
            $answers = $this->getapp()->AnswerModel->getAnswersByTopicId($this->getRequest()->getParam('id'));
            $this->getApp()->TopicModel->visit($topic['id']);
            $tags = [];
            $tagsResponse = $this->getApp()->TopicModel->getTopicTags($topic['id']);
            
            foreach ($tagsResponse as $tagResponse) {
                $tags[] = $tagResponse['tag'];
            }
            
            $this->getView()->isOwnTopic = (isset($_SESSION['user_id']) && $topic['user_id'] == $_SESSION['user_id']);
            $this->getView()->isAdmin = $this->isAdmin;
            $this->getView()->topic = $topic;
            $this->getView()->answers = $answers;
            $this->getView()->tags = implode(', ', $tags);
            $this->getView()->isLogged = $this->getApp()->UserModel->isLogged();
        }
    }

    public function add() {
        
        if (!$this->getApp()->UserModel->isLogged()) {
            $this->redirect('welcome', 'index', 'login', 'required');
        }
        
        if (!$this->getRequest()->getParam('forumid')) {
            die(json_encode(['success' => 0]));
        }
        
        if ($this->getRequest()->getPost()->getParam('summary')) {
            if (!$this->isCsrfTokenValid()) {
                die(json_encode(array('success' => 0, 'msg' => 'Wrong CSRF Token')));
            }
            $summary = $this->getRequest()->getPost()->getParam('summary');
            $body = $this->getRequest()->getPost()->getParam('body');
            $forum_id = $this->getRequest()->getParam('forumid');
            $user_id = $_SESSION['user_id'];
            
            $tags = explode(',', $this->getRequest()->getPost()->getParam('tags'));

            if (true == ($response = $this->getApp()->TopicModel->add($summary, $body, $forum_id, $user_id))) {
                foreach ($tags as $tag) {
                    if (!$this->getApp()->TopicModel->addTag($response['id'], trim($tag))) {
                        die(json_encode(['success' => 0]));
                    }    
                }
                die(json_encode(['success' => 1, 'topic_id' => $response['id']]));
            }

            die(json_encode(['success' => 0]));
        }
    }

    public function find() {

        $result = ['success' => 0];

        if ($this->getRequest()->getPost()->getParam('keyword')) {
            $result = $this->getApp()->TopicModel->find($this->getRequest()->getPost()->getParam('keyword'));
        }
        
        if ($this->getRequest()->getPost()->getParam('tag')) {
            $result = $this->getApp()->TopicModel->findTopicsByTag($this->getRequest()->getPost()->getParam('tag'));
        }
        
        foreach ($result as &$row) {
            $row['summary'] = htmlentities($row['summary']);
            $row['summary'] = htmlentities($row['body']);
        }

        die(json_encode($result));
    }
    
    public function edit() {
        if ($this->getRequest()->getParam('id')) {
            if (!$this->isCsrfTokenValid()) {
                die(json_encode(array('success' => 0, 'msg' => 'Wrong CSRF Token')));
            }
            $topic_id = $this->getRequest()->getParam('id');
            $topic = $this->getApp()->TopicModel->getTopicById($topic_id);

            $isOwnTopic = (isset($_SESSION['user_id']) && $topic['user_id'] == $_SESSION['user_id']);
            
            $summary = $this->getRequest()->getPost()->getParam('summary');
            $body = $this->getRequest()->getPost()->getParam('body');
            $tags = explode(',', $this->getRequest()->getPost()->getParam('tags'));
            
            if ($isOwnTopic || $this->isAdmin) {
                if ($this->getApp()->TopicModel->edit($topic_id, $summary, $body, $tags)) {
                    die(json_encode(['success' => 1]));
                }
            }
        }
        
        die(json_encode(['success' => 0]));
    }
    
    public function close() {
        if ($this->getRequest()->getParam('id')) {
            if (!$this->isCsrfTokenValid()) {
                die(json_encode(array('success' => 0, 'msg' => 'Wrong CSRF Token')));
            }
            $topic_id = $this->getRequest()->getParam('id');
            $topic = $this->getApp()->TopicModel->getTopicById($topic_id);

            $isOwnTopic = (isset($_SESSION['user_id']) && $topic['user_id'] == $_SESSION['user_id']);
           
            if ($isOwnTopic || $this->isAdmin) {
                if ($this->getApp()->TopicModel->close($topic_id)) {
                    die(json_encode(['success' => 1]));
                }
            }
        }
        die(json_encode(['success' => 0]));
    }
    
    public function reopen() {
        if ($this->getRequest()->getParam('id')) {
            if (!$this->isCsrfTokenValid()) {
                die(json_encode(array('success' => 0, 'msg' => 'Wrong CSRF Token')));
            }
            $topic_id = $this->getRequest()->getParam('id');

            if ($this->isAdmin) {
                if ($this->getApp()->TopicModel->reopen($topic_id)) {
                    die(json_encode(['success' => 1]));
                }
            }
        }
        
        die(json_encode(['success' => 0]));
    }
    
    public function delete() {
        if ($this->getRequest()->getParam('id')) {
            if (!$this->isCsrfTokenValid()) {
                die(json_encode(array('success' => 0, 'msg' => 'Wrong CSRF Token')));
            }
            $topic_id = $this->getRequest()->getParam('id');
            $topic = $this->getApp()->TopicModel->getTopicById($topic_id);

            $isOwnTopic = (isset($_SESSION['user_id']) && $topic['user_id'] == $_SESSION['user_id']);
           
            if ($isOwnTopic || $this->isAdmin) {
                if ($this->getApp()->TopicModel->delete($topic_id)) {
                    die(json_encode(['success' => 1]));
                }
            }
        }
        
        die(json_encode(['success' => 0]));
    }

}

