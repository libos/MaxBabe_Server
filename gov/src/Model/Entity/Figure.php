<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Figure Entity.
 */
class Figure extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'filename' => true,
        'path' => true,
        'md5' => true,
        'weather' => true,
        'ge_hour' => true,
        'le_hour' => true,
        'ge_week' => true,
        'le_week' => true,
        'ge_month' => true,
        'le_month' => true,
        'ge_temp' => true,
        'le_temp' => true,
        'ge_aqi' => true,
        'le_aqi' => true,
        'update_flag'=>true,
        'reso' => true,
        'user_id' => true,
        'user' => true,
    ];
}
