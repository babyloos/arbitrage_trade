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
    function updateValue() {
        $coincheckValue = $this->coincheckApi->ticker->all();
        $this->valueData["coincheckBid"] = (double)$coincheckValue["bid"];
        $this->valueData["coincheckAsk"] = (double)$coincheckValue["ask"];
        $this->valueData["zaifBid"] = (double)$this->Zaif->pub("ticker", "btc_jpy")->bid;
        $this->valueData["zaifAsk"] = (double)$this->Zaif->pub("ticker", "btc_jpy")->ask;
        
        if($this->Value->find()->count() == 0) {
            $record = $this->Value->newEntity([
                    "coincheck_bid" => $this->valueData["coincheckBid"],
                    "coincheck_ask" => $this->valueData["coincheckAsk"],
                    "zaif_bid" => $this->valueData["zaifBid"],
                    "zaif_ask" => $this->valueData["zaifAsk"]
                ]);
            $this->Value->save($record);
        } else {
            $record = $this->Value->find()->first();
            $record->coincheck_bid = $this->valueData["coincheckBid"];
            $record->coincheck_ask = $this->valueData["coincheckAsk"];
            $record->zaif_bid = $this->valueData["zaifBid"];
            $record->zaif_ask = $this->valueData["zaifAsk"];
            $this->Value->save($record);
        }
        
        // return $this->valueData;
    }
    
    // 両取引所ごとの資産、全体の資産を計算、取得する
    // DBの更新を行う
    function updateAsset() {
        // coincheckの資産取得
        /*
        $coincheckAsset = $this->coincheckApi->account->balance();
        // if(!$coincheckAsset["success"]) throw new Exception("coincheck APIエラー");
        $this->assetData["coincheckJpy"] = (double)$coincheckAsset["jpy"];
        $this->assetData["coincheckBtc"] = (double)$coincheckAsset["btc"];
        // zaifの資産取得(本人確認後実装)
        // エラー処理
        $this->assetData["zaifJpy"] = 100000.0;
        $this->assetData["zaifBtc"] = 10.0;
        
        // 全体の資産合計
        $this->assetData["totalBtc"] = $this->assetData["coincheckBtc"] + $this->assetData["zaifBtc"];
        $this->assetData["totalJpy"] = $this->assetData["coincheckJpy"] + $this->assetData["zaifJpy"];
        
        // 評価額合計
        $btcValue = $this->valueData["coincheckBid"] > $this->valueData["zaifBid"] ? $this->valueData["coincheckBid"] : $this->valueData["zaifBid"];
        $this->assetData["totalValuation"] = $this->assetData["totalJpy"] + ($this->assetData["totalBtc"] * $btcValue);
        */
        
        // debug
        $this->assetData["coincheckJpy"] = 500000;
        $this->assetData["coincheckBtc"] = 10;
        $this->assetData["zaifJpy"] = 500000;
        $this->assetData["zaifBtc"] = 10;
        
        
        // DBに保存
        if($this->Asset->find()->count() == 0) {
            $record = $this->Asset->newEntity([
                    "coincheck_jpy" => $this->assetData["coincheckJpy"],
                    "coincheck_btc" => $this->assetData["coincheckBtc"],
                    "zaif_jpy" => $this->assetData["zaifJpy"],
                    "zaif_btc" => $this->assetData["zaifBtc"],
                ]);
            $this->Asset->save($record);
        } else {
            $record = $this->Asset->find()->first();
            // 更新処理
            $record->coincheck_jpy = $this->assetData["coincheckJpy"];
            $record->coincheck_btc = $this->assetData["coincheckBtc"];
            $record->zaif_jpy = $this->assetData["zaifJpy"];
            $record->zaif_btc = $this->assetData["zaifBtc"];
            $this->Asset->save($record);
        }
        
        // return $this->assetData;
    }
    
    // 資産情報を取得する
    function getAsset() {
        // $assetData = $this->Asset->find()->first();
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
        $valueData = $this->Value->find()->first();
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
    
    // 両取引所の残高を確認し、調整する。
    function adjustAsset() {
        // coincheckから他口座への手数料0.0005BTC
        // 規定数量よりも少なくなっている場合は反対サイトから送付する
        // JPYは送金できないので、BTC残量だけを監視し、反対サイトへ送付する
        // coincheckの資産を確認
        //      規定数量より減っている場合はzaifから送付
        // zaifの資産を確認
        //      規定数量より減っている場合はcoincheckから送付
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