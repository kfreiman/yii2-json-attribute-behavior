<?php

namespace kfreiman\yii\behaviors\model;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Class SerializedAttributes
 * @package baibaratsky\yii\behaviors\model
 *
 * @property BaseActiveRecord $owner
 */
class JsonAttributeBehavior extends Behavior
{
    /**
     * @var string[] Attributes you want to be encoded
     */
    public $attributes = [];

    /**
     * @var array store old attributes
     */
    private $_oldAttributes = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'encodeAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encodeAttributes',

            BaseActiveRecord::EVENT_AFTER_INSERT => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_FIND => 'decodeAttributes',
        ];
    }

    public function encodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            if (isset($this->_oldAttributes[$attribute])) {
                $this->owner->setOldAttribute($attribute, $this->_oldAttributes[$attribute]);
            }

            $this->owner->$attribute = json_encode($this->owner->$attribute);
        }
    }

    public function decodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $this->_oldAttributes[$attribute] = $this->owner->getOldAttribute($attribute);

            $value = json_decode($this->owner->$attribute);
            $this->owner->setAttribute($attribute, $value);
            $this->owner->setOldAttribute($attribute, $value);
        }
    }
}
