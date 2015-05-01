<?php

namespace ANSR\Controllers;

/**
 * Topics Controller
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class Welcome extends Controller {
    public function index() {
        $this->getView()->loginRequired = false;
        
        $categories = $this->getApp()->CategoryModel->getCategories();
        
        foreach ($categories as &$category) {
            $category['forums'] = $this->getApp()->ForumModel->getForumsByCategoryId($category['id']);
        }
        
        if ($this->getRequest()->getParam('login')) {
            $this->getView()->loginRequired = true;
        }

        $this->getView()->categories = $categories;
    }
}