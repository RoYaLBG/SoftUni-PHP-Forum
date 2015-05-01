<?php

namespace ANSR\Controllers;

class Partials extends Controller {

    public function aside(){
        $this->getView()->sections = Array("C++", "C#", "Java", "JavaScript", "Perl", "PHP", "Python", "Ruby");
        //var_dump($this->getView()->sections);
        for ($i = 0; $i < count($this->getView()->sections); $i++) {

        }
        foreach ($this->getView()->sections as $value) {

        }

    }


}