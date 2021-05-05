<?php

namespace modules\abac\src\forms;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the Form class for table "abac_policy".
 *
 * @property int|null $ap_id
 * @property string|null $ap_subject_json
 * @property string $ap_object
 * @property string|null $ap_action_list
 * @property int $ap_effect
 * @property string|null $ap_title
 * @property int|null $ap_sort_order
 */
class AbacPolicyForm extends Model
{

    public $ap_id;
    public $ap_subject_json;
    public $ap_object;
    public $ap_action_list;
    public $ap_effect;
    public $ap_title;
    public $ap_sort_order;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ap_object', 'ap_effect', 'ap_action_list'], 'required'],
            [['ap_subject_json', 'ap_action_list'], 'safe'],
            [['ap_effect', 'ap_sort_order', 'ap_id'], 'integer'],
            [['ap_object', 'ap_title'], 'string', 'max' => 255],
        ];
    }


    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'ap_id' => 'Id',
            'ap_subject_json' => 'Subject',
            'ap_object' => 'Object',
            'ap_action_list' => 'Action List',
            'ap_effect' => 'Effect',
            'ap_title' => 'Title',
            'ap_sort_order' => 'Sort Order',
        ];
    }

    /**
     * @return array
     */
    public function getActionList(): array
    {
        $list = Yii::$app->abac->getActionListByObject($this->ap_object);
        return $list;
    }

    public function getObjectList(): array
    {
        $list = Yii::$app->abac->getObjectList();
        return $list;
    }

    public function getObjectAttributeList()
    {
        $list = Yii::$app->abac->getAttributeListByObject($this->ap_object);
        return $list;
    }
}
