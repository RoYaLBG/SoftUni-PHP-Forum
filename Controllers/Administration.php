<?php

namespace ANSR\Controllers;

/**
 * Administration Controller
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class Administration extends Controller {
    
    public function init() {
        parent::init();
        
        if (!$this->isAdmin) {
            $this->redirect('welcome', 'index');
        }
    }
    
    public function index() {
        $forums = $this->getApp()->ForumModel->getForums();
        $this->getView()->forums = $forums;
    }
    
    public function editForums() {
        if ($this->getRequest()->getParam('id')) {
            $id = $this->getRequest()->getParam('id');
            $this->getView()->categories = $this->getApp()->CategoryModel->getCategories();
            $this->getView()->forum = $this->getApp()->ForumModel->getForumById($id);
            
            if ($this->getRequest()->getPost()->getParam('submit')) {
                $name = $this->getRequest()->getPost()->getParam('name');
                $category_id = $this->getRequest()->getPost()->getParam('category');
                $this->getApp()->ForumModel->edit($id, $name, $category_id);
                $this->redirect('administration', 'index');
            }
        } else {
            $this->getView()->error = 'No forum selected';
        }
    }
    
    public function addForum() {
        $this->getView()->categories = $this->getApp()->CategoryModel->getCategories();
        
        if ($this->getRequest()->getPost()->getParam('submit')) {
            $name = $this->getRequest()->getPost()->getParam('name');
            $category_id = $this->getRequest()->getPost()->getParam('category');
            $this->getApp()->ForumModel->add($name, $category_id, 1);
            $this->redirect('administration', 'index');
        }
    }
    
    public function addCategory() {
        if ($this->getRequest()->getPost()->getParam('name')) {
            $name = $this->getRequest()->getPost()->getParam('name');
            
            if ($this->getApp()->CategoryModel->add($name)) {
                die(json_encode(['success' => 1]));
            }
        }
        
        die(json_encode(['success' => 0]));
    }

}