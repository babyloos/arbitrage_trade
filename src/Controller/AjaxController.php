<?php

namespace App\Controller;

class AjaxController extends AppController {
 
    public function initialize() {
        parent::initialize();
        $this->loadComponent('ArbitrageCalc');
        $this->loadComponent('RequestHandler');
        $this->autoRender = FALSE;
    }
    
    public function index() {
        /*
        if (!$this->request->is(['ajax'])) {
			// ブラウザから直接アクセスさせたくない場合、ここで例外を投げることもできます。
			// throw new ForbiddenException();
			$this->autoRender = FALSE;
			return;
		}
		*/
		
	    $jsonData = [];
	    $value = $this->ArbitrageCalc->getValue();
	    $asset = $this->ArbitrageCalc->getAsset();
	    $diff = $this->ArbitrageCalc->getDiff();
	    
	    $jsonData["value"] = $value;
	    $jsonData["asset"] = $asset;
	    $jsonData["diff"] = $diff;
	   
		echo json_encode($jsonData);
	    
	   // $this->set('jsonData', $jsonData);
        // JsonView がシリアライズするべきビュー変数を指定する
        // $this->set('_serialize', ['jsonData']);
	   
    }
}