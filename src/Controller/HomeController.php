<?php

namespace App\Controller;

class HomeController extends AppController {
    
    public function initialize() {
        parent::initialize();
         $this->loadComponent('ArbitrageCalc');
    }
    
    
    public function index() {
        // echo "index";
        echo $this->ArbitrageCalc->calc(10, 20);
    }
}