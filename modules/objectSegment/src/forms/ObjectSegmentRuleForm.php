<?php

namespace modules\objectSegment\src\forms;

use modules\objectSegment\src\entities\ObjectSegmentTypeQuery;
use modules\objectSegment\src\service\ObjectSegmentService;
use Yii;
use yii\base\Model;

class ObjectSegmentRuleForm extends Model
{
    public $osr_id;
    public $osr_osl_id;
    public $osr_rule_condition_json;
    public $osr_title;
    public $osr_enabled;
    public $objectName = '';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['osr_osl_id'], 'required'],
            [['osr_rule_condition_json'], 'safe'],
            [['osr_osl_id'], 'integer'],
            [['osr_title'], 'string', 'max' => 255],
            [['osr_enabled'], 'boolean'],
            [['osr_rule_condition_json'], 'validateSubject', 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    public function getObjectAttributeList()
    {
        $objectName = null;
        if ($this->osr_osl_id) {
            $objectName = ObjectSegmentTypeQuery::getObjectNameByOsrOslId($this->osr_osl_id);
            $this->objectName = $objectName;
        }
        $list = Yii::$app->objectSegment->getAttributeListByObject($objectName);
        return $list;
    }

    public function getObjectList(): array
    {
        $list = Yii::$app->objectSegment->getObjectList();
        return $list;
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'osr_id' => 'Id',
            'osr_rule_condition_json' => 'Rules Condition',
            'osr_osl_id' => 'List',
            'osr_title' => 'Title',
            'osr_enabled' => 'Enabled',
        ];
    }

    /**
     * @return string
     */
    public function getDecodeCode(): string
    {
        $code = '';
        $rules = @json_decode($this->osr_rule_condition_json, true);
        if (is_array($rules)) {
            $code = ObjectSegmentService::conditionDecode($rules, '');
        }
        return $code;
    }
    /**
     * @param $attribute
     * @param $params
     */
    public function validateSubject($attribute, $params)
    {
        $code = $this->getDecodeCode();

        if (!$code) {
            $this->addError('osr_rule_condition_json', 'Invalid Expression Language: "' . $code . '"');
        }
    }
}
