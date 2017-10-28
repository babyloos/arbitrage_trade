<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\ArbitrageCalcComponent;

/**
 * 資産情報、価格情報の更新処理を行う
 */
class UpdateShell extends Shell
{
    public function initialize() {
        parent::initialize();
        $this->loadModel('Asset');
        $this->loadModel('Value');
        $this->ArbitrageCalc = new ArbitrageCalcComponent(new ComponentRegistry(), [/*引数*/]);
    }
    public function main()
    {
        while(1) {
            sleep(3);
            $this->update();
            $this->trade();
        }
    }
    
    public function initAsset() {
        $this->ArbitrageCalc->initAsset();
    }
    
    // 資産情報、価格情報を更新する
    private function update() {
        $this->ArbitrageCalc->updateAsset($this->Asset->find()->last());
        $this->ArbitrageCalc->updateValue();
    }
    
    // 実際の取引を行う
    private function trade() {
        // 売買益が指定金額以上の場合実行する
        $desProfit = 100;
        $btcAmount = 1;
        $diff = $this->ArbitrageCalc->getDiff();
        if($diff["buy_coincheck"] >= $desProfit) {
            $this->out("CoincCheckで購入");
            if($this->ArbitrageCalc->buyCoincheck($btcAmount)) {
                $this->out("購入成功");
            } else {
                $this->out("購入失敗");
                $this->ArbitrageCalc->adjustAsset();
            }
            // $this->ArbitrageCalc->buyCoincheck($btcAmount) ? $this->out("購入成功") : $this->out("購入失敗");
        } else if($diff["buy_zaif"] >= $desProfit) {
            $this->out("Zaifで購入");
            if($this->ArbitrageCalc->buyZaif($btcAmount)) {
                $this->out("購入成功");
            } else {
                $this->out("購入失敗");
                $this->ArbitrageCalc->adjustAsset();
            }
            // $this->ArbitrageCalc->buyZaif($btcAmount) ? $this->out("購入成功") : $this->out("購入失敗");
        } else {
            $this->out("売買は行われなかった");
        }
        
        echo "buy_coincheck : " . $diff["buy_coincheck"] . "\n";
        echo "buy_zaif : " . $diff["buy_zaif"] . "\n";
    }
}