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
		
		// データ用意
		// データの更新作業は定期実行で行うので、ここでは行わない。
        // $valueData = $this->ArbitrageCalc->getValue();
        // $assetData = $this->ArbitrageCalc->getAsset();
        // 差額取得
        // $diffData = $this->ArbitrageCalc->getDiff();
       
	    // json送信
	    // var_dump($valueData);
	    // var_dump($assetData);
	    
	    $jsonData = [];
	    $value = $this->ArbitrageCalc->getValue();
	    $asset = $this->ArbitrageCalc->getAsset();
	    $diff = $this->ArbitrageCalc->getDiff();
	    
	    $jsonData["value"] = $value;
	    $jsonData["asset"] = $asset;
	    $jsonData["diff"] = $diff;
	    
	    /*
	    foreach($valueData as $data) {
	        $jsonData[] = 
	    }
	    
	    foreach($assetData as $data) {
	        array_push($jsonData["asset"], $data);
	    }
	    
	    foreach($diffData as $data) {
	        array_push($jsonData["diff"], $data);
	    }
	    */
	    
	   // var_dump($jsonData);
	   
	   echo json_encode($jsonData);
	    
	   // $this->set('jsonData', $jsonData);
        // JsonView がシリアライズするべきビュー変数を指定する
        // $this->set('_serialize', ['jsonData']);
	   
    }
}