<?php

namespace App\Controller\Component;

use Coincheck\Coincheck;
use Cake\Controller\Component;
// use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
/**
 * アービトラージ取引に利用するすべてのコンポーネント
 * 
 */
class ArbitrageCalcComponent extends Component
{
    
    public $components = ['Zaif'];
    
    function initialize(array $config) {
    
        $this->Account = TableRegistry::get("ExchangesAccount");
        $this->Asset = TableRegistry::get("Asset");
        $this->Value = TableRegistry::get("Value");
        
        $this->accountKey = $this->Account->find()->first();
        $this->coincheckApiKey = $this->accountKey->coincheck_api_key;
        $this->coincheckSecretKey = $this->accountKey->coincheck_secret_key;
        $this->coincheckApi = new Coincheck($this->coincheckApiKey, $this->coincheckSecretKey);
        // 資産情報
        $this->assetData = [];
        // 価格情報
        $this->valueData = [];
        // 差額情報
        $this->diffData = [];
    }
    
    // coincheckとzaifの価格情報を取得する
    // DBの更新を行う
    // DBに履歴を蓄積する
    function updateValue() {
        $coincheckValue = $this->coincheckApi->ticker->all();
        $this->valueData["coincheckBid"] = (double)$coincheckValue["bid"];
        $this->valueData["coincheckAsk"] = (double)$coincheckValue["ask"];
        $this->valueData["zaifBid"] = (double)$this->Zaif->pub("ticker", "btc_jpy")->bid;
        $this->valueData["zaifAsk"] = (double)$this->Zaif->pub("ticker", "btc_jpy")->ask;
        
        $record = $this->Value->newEntity([
                "coincheck_bid" => $this->valueData["coincheckBid"],
                "coincheck_ask" => $this->valueData["coincheckAsk"],
                "zaif_bid" => $this->valueData["zaifBid"],
                "zaif_ask" => $this->valueData["zaifAsk"]
            ]);
        $this->Value->save($record);
    }
    
    // 両取引所ごとの資産、全体の資産を計算、取得する
    // DBの更新を行う
    // DBに履歴を蓄積する
    function updateAsset($asset=false) {
        // coincheckの資産取得
        /*
            TODO:
                実際の資産を取り扱う
        */
        
        if(!$asset) {
            if($this->Asset->find()->count() == 0) {
                $this->initAsset();
                return;
            }
        }
        
        // DBに保存
        $record = $this->Asset->newEntity([
                "coincheck_jpy" => $asset->coincheck_jpy,
                "coincheck_btc" => $asset->coincheck_btc,
                "zaif_jpy" => $asset->zaif_jpy,
                "zaif_btc" => $asset->zaif_btc,
            ]);
        $this->Asset->save($record);
    }
    
    function initAsset() {
        if($this->Asset->find()->count() == 0) {
            $record = $this->Asset->newEntity([
                    "coincheck_jpy" => 10000000,
                    "coincheck_btc" => 10,
                    "zaif_jpy" => 10000000,
                    "zaif_btc" => 10,
                ]);
            $this->Asset->save($record);
        }
    }
    
    // 資産情報を取得する
    function getAsset() {
        $assetData = $this->Asset->find()->last();
        $assetData->total_jpy = $assetData->coincheck_jpy + $assetData->zaif_jpy;
        $assetData->total_btc = $assetData->coincheck_btc + $assetData->zaif_btc;
        $valueData = $this->getValue();
        $btcValue = $valueData->coincheck_bid > $valueData->zaif_bid ? $valueData->coincheck_bid : $valueData->zaif_bid;
        $assetData->total_valuation = $assetData->total_jpy + ($assetData->total_btc * $btcValue);
        return $assetData;
    }
    
    // 価格情報を取得する
    // DBから取得
    function getValue() {
        $valueData = $this->Value->find()->last();
        return $valueData;
    }
    
    // 差額情報を取得する
    // TODO: DBの情報を使って計算する
    function getDiff() {
        /*
            // coincheckで買って、zaifで売った場合
            $buyCoincheckSellZaif = $this->valueData["zaifBid"] - $this->valueData["coincheckAsk"];
            // zaifで買って、coincheckで売った場合
            $buyZaifSellCoincheck = $this->valueData["coincheckBid"] - $this->valueData["zaifAsk"];
            $this->diffData["buyCoincheck"] = $buyCoincheckSellZaif;
            $this->diffData["buyZaif"] = $buyZaifSellCoincheck;
            return $this->diffData;
        */
        $value = $this->getValue();
        $buyCoincheckSellZaif = $value->zaif_bid - $value->coincheck_ask;
        $buyZaifSellCoincheck = $value->coincheck_bid - $value->zaif_ask;
        $diffData = [];
        $diffData["buy_coincheck"] = $buyCoincheckSellZaif;
        $diffData["buy_zaif"] = $buyZaifSellCoincheck;
        
        return $diffData;
    }
    
    // BTC購入販売処理
    
    // coincheckで購入し、zaifで販売する
    function buyCoincheck($btcAmount) {
        $asset = $this->getAsset();
        $value = $this->getValue();
        if($asset->coincheck_jpy >= $btcAmount * $value->coincheck_bid && $asset->zaif_btc >= $btcAmount) {
            $asset->coincheck_btc += $btcAmount;
            $asset->coincheck_jpy -= $btcAmount * $value->coincheck_ask;
            $asset->zaif_btc -= $btcAmount;
            $asset->zaif_jpy += $btcAmount * $value->zaif_bid;
            $asset->total_jpy = $asset->coincheck_jpy + $asset->zaif_jpy;
            $asset->total_btc = $asset->coincheck_btc + $asset->zaif_btc;
            // var_dump($asset->coincheck_jpy);
            $this->updateAsset($asset);
            $ret = true;
        } else {
            $ret = false;
        }
        
        return $ret;
    }
    
    // zaifで購入し、coincheckで販売する
    function buyZaif($btcAmount) {
        $asset = $this->getAsset();
        $value = $this->getValue();
        if($asset->zaif_jpy >= $btcAmount * $value->zaif_ask && $asset->coincheck_btc >= $btcAmount) {
            $asset->zaif_btc += $btcAmount;
            $asset->zaif_jpy -= $btcAmount * $value->zaif_ask;
            $asset->coincheck_btc -= $btcAmount;
            $asset->coincheck_jpy += $btcAmount * $value->coincheck_bid;
            $this->updateAsset($asset);
            $ret = true;
        } else {
            $ret = false;
        }
        
        return $ret;
    }
    
    // 両取引所の残高を確認し、調整する。
    function adjustAsset() {
        // 手数料もシミュレーションする
        // coincheckから他口座への手数料0.0005BTC
        // zaifからcoincheckも同じ
      
        // debug
        // とりあえずJPY, BTC共に同数になるように調整
        $asset = $this->getAsset();
        $jpyDiff = $asset->coincheck_jpy - $asset->zaif_jpy;
        $btcDiff = $asset->coincheck_btc - $asset->zaif_btc;
        if($jpyDiff > 0) {
            $asset->coincheck_jpy -= $jpyDiff / 2;
            $asset->zaif_jpy += $jpyDiff / 2;
        } else {
            $asset->zaif_jpy -= $jpyDiff / 2 * -1;
            $asset->coincheck_jpy += $jpyDiff / 2 * -1;
        }
        
        if($btcDiff > 0) {
            $asset->coincheck_btc -= $btcDiff / 2;
            $asset->zaif_btc += $btcDiff / 2;
        } else {
            $asset->zaif_btc -= $btcDiff / 2 * -1;
            $asset->coincheck_btc += $btcDiff / 2 * -1;
        }
        
        $this->updateAsset($asset);
    }
    
    function sendBtc($fromExchange, $amount) {
        $asset = $this->Asset->find()->last();
        if($fromExchange = "coincheck") {
            if($asset->coincheck_btc >= $amount) {
                $asset->coincheck_btc -= $amount;
                $asset->zaif_btc += $amount;
                $this->updateAsset($asset);
            } else {
                return false;
            }
        } else if($fromExchange == "zaif") {
            if($asset->zaif_btc >= $amount) {
                $asset->zaif_btc -= $amount;
                $asset->coincheck_btc += $amount;
                $this->updateAsset($asset);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    function sendJpy($fromExchange, $amount) {
        $asset = $this->Asset->find()->last();
        if($fromExchange = "coincheck") {
            if($asset->coincheck_jpy >= $amount) {
                $asset->coincheck_jpy -= $amount;
                $asset->zaif_jpy += $amount;
                $this->updateAsset($asset);
            } else {
                return false;
            }
        } else if($fromExchange == "zaif") {
            if($asset->zaif_jpy >= $amount) {
                $asset->zaif_jpy -= $amount;
                $asset->coincheck_jpy += $amount;
                $this->updateAsset($asset);
            } else {
                return false;
            }
        } else {
            return false;
        }
     }
     
    // 資産履歴を取得
    public function getAssetHistory($limit) {
        $assetHistory = $this->Asset->find()->limit($limit)->toArray();
        $valueData = $this->getValue();
        foreach($assetHistory as &$asset) {
            $asset->total_jpy = $asset->coincheck_jpy + $asset->zaif_jpy;
            $asset->total_btc = $asset->coincheck_btc + $asset->zaif_btc;
            $btcValue = $valueData->coincheck_bid > $valueData->zaif_bid ? $valueData->coincheck_bid : $valueData->zaif_bid;
            $asset->total_valuation = $asset->total_jpy + ($asset->total_btc * $btcValue);
        }
        return $assetHistory;
    }

    public function test()
    {
       // return $this->getAsset();
       // $this->updateValue();
    }
}

// sqlメモ
/*

create table exchanges_account (
    id int auto_increment primary key not null,
    coincheck_api_key varchar(255),
    coincheck_secret_key varchar(255),
    zaif_api_key varchar(255),
    zaif_secret_key varchar(255),
    created datetime not null,
    modified datetime not null
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table asset (
    id int auto_increment primary key not null,
    coincheck_jpy double,
    coincheck_btc double,
    zaif_jpy double,
    zaif_btc double,
    created datetime not null,
    modified datetime not null
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table value (
    id int auto_increment primary key not null,
    coincheck_bid double,
    coincheck_ask double,
    zaif_bid double,
    zaif_ask double,
    created datetime not null,
    modified datetime not null
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/