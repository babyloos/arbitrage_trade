<div>
    <?= $this->cell('AssetHistory')->render() ?>
</div>

<div>
    <table>
        <caption>Asset History</caption>
        <thead>
            <tr>
                <th>ID</th>
                <th>coincheck JPY</th>
                <th>coincheck BTC</th>
                <th>zaif JPY</th>
                <th>zaif BTC</th>
                <th>total JPY</th>
                <th>total BTC</th>
                <th>total Valuation</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($assetHistory as $asset): ?>
                <tr>
                    <td><?= $asset->id ?></td>
                    <td><?= $asset->coincheck_jpy ?></td>
                    <td><?= $asset->coincheck_btc ?></td>
                    <td><?= $asset->zaif_jpy ?></td>
                    <td><?= $asset->zaif_btc ?></td>
                    <td><?= $asset->total_jpy ?></td>
                    <td><?= $asset->total_btc ?></td>
                    <td><?= $asset->total_valuation ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>