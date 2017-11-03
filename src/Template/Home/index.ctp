<div class="tables">
        <table>
            <caption>Asset</caption>
            <thead>
                <tr>
                    <th></th><th>coincheck</th><th>zaif</th><th>total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>JPY</td><td id="coincheckJpy"></td><td id="zaifJpy"></td><td id="totalJpy"></td>
                </tr>
                <tr>
                    <td>BTC</td><td id="coincheckBtc"></td><td id="zaifBtc"></td><td id="totalBtc"></td>
                </tr>
                <tr>
                    <td>valuation</td><td></td><td></td><td id="totalValuation"></td>
                </td>
            <tbody>
            </thead>
        </table>
        
        <table>
            <caption>Now Valuation</caption>
            <thead>
                <tr>
                    <th></th><th>coincheck</th><th>zaif</th>
                </tr>
            <tbody>
                <tr>
                    <td>Bid</td><td id="coincheckBid"></td><td id="zaifBid"></td>
                </tr>
                <tr>
                    <td>Ask</td><td id="coincheckAsk"></td><td id="zaifAsk"></td>
                </tr>
            </tbody>
            </thead>
        </table>
        
        <table>
            <caption>Diff Data</caption>
            <thead>
                <tr>
                    <th></th><th>buy coincheck sell zaif</th><th>buy zaif sell coincheck</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>profit</td><td id="buyCoincheck"></td>><td id="buyZaif"></td>
                </tr>
            </tbody>
        </table>
</div>

<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>

<!-- jsonデータ取得用 -->
<script>
    // var csrf = $('input[name=_csrfToken]').val();
    var url = "/arbitrage_trade/ajax";
    ajax();
    setInterval(function(){
        ajax();
    }, 3000);
    
    function ajax() {
        $.ajax({
            url: url,
            type: "POST",
            /*
                beforeSend: function(xhr){
                    xhr.setRequestHeader('X-CSRF-Token', csrf);
                },
            */
            dataType: "JSON",
            success : function(data, dataType){
                dataSet(data);
            },
            error: function(data, dataType){
            }
        });
    }
    
    function dataSet(data) {
        // console.log(data);
        $("#coincheckJpy").text(data.asset.coincheck_jpy);
        $("#coincheckBtc").text(data.asset.coincheck_btc);
        $("#zaifJpy").text(data.asset.zaif_jpy);
        $("#zaifBtc").text(data.asset.zaif_btc);
        $("#totalJpy").text(data.asset.total_jpy);
        $("#totalBtc").text(data.asset.total_btc);
        $("#totalValuation").text(data.asset.total_valuation);
        $("#coincheckBid").text(data.value.coincheck_bid);
        $("#coincheckAsk").text(data.value.coincheck_ask);
        $("#zaifBid").text(data.value.zaif_bid);
        $("#zaifAsk").text(data.value.zaif_ask);
        $("#buyCoincheck").text(data.diff.buy_coincheck);
        $("#buyZaif").text(data.diff.buy_zaif);
    }
</script>
