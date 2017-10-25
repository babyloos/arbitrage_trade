<?php

namespace App\Controller;

class HomeController extends AppController {
    
    public function initialize() {
        parent::initialize();
        $this->loadComponent('ArbitrageCalc');
    }
    
    public function index() {
        $this->ArbitrageCalc->test();
    }
}

/*
AssetModel
    資産情報
    coincheckでの資産(JPY, BTC)
    zaifでの資産(JPY, BTC)

ValueModel
    価格情報
    coincheckでの買取、販売レート
    zaifでの買取、販売レート
*/