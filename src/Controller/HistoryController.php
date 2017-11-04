<?php

namespace App\Controller;
// use Cake\Datasource\ConnectionManager;

class HistoryController extends AppController {
    
    // public $paginate = [
    //     'limit' => 50,
    //     'order' => [
    //         'Asset.created' => 'desc'
    //     ]
    // ];
    
    public function initialize() {
        parent::initialize();
        $this->loadComponent('ArbitrageCalc');
        // $this->loadComponent('Paginator');
    }
    
    // 履歴表示
    public function index($page=1) {
        // 資産情報の履歴を１００件取得
        $assetHistory = $this->ArbitrageCalc->getAssetHistory(100);
        $this->set("assetHistory", $assetHistory);
        // $this->set("asset", $this->paginate());
        
        // $assetHit
        
    }
    
    
    // 推移グラフ
    public function graph() {
        
    }
    
}