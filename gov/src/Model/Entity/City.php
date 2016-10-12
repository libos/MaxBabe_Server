<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * City Entity.
 */
class City extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'pinyin' => true,
        'level2' => true,
        'province' => true,
        'country' => true,
        'uuid' => true,
        'aqi_uuid' => true,
        'englishname' => true,
        'ext' => true,
        'datafrom' => true,
        'aqifrom' => true,
    ];
}
