<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Asset Entity
 *
 * @property int $id
 * @property float $coincheck_jpy
 * @property float $coincheck_btc
 * @property float $zaif_jpy
 * @property float $zaif_btc
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class Asset extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
