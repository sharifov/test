<?php

namespace modules\objectSegment\src\forms;

use common\components\validators\KeyValidator;
use yii\base\Model;
use Yii;

class ObjectSegmentListForm extends Model
{
    public $osl_id;
    public $osl_ost_id;
    public $osl_key;
    public $osl_description;
    public $osl_title;
    public $osl_enabled;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['osl_ost_id', 'osl_title', 'osl_key'], 'required'],
            [['osl_key'], KeyValidator::class],

            [['osl_ost_id'], 'integer'],
            [['osl_key'], 'string', 'max' => 100],
            [['osl_title'], 'string', 'max' => 100],
            [['osl_description'], 'string', 'max' => 1000],
            [['osl_enabled'], 'boolean'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'osl_id' => 'Id',
            'osl_ost_id' => 'Type',
            'osl_key' => 'Key',
            'osl_title' => 'Title',
            'osl_description' => 'Description',
            'osl_enabled' => 'Enabled',
        ];
    }

    public function getObjectTypeList()
    {
        $list = Yii::$app->objectSegment->getObjectTypes();
        return $list;
    }
}
