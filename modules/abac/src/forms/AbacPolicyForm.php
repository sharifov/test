<?php

namespace modules\abac\src\forms;

use modules\abac\src\AbacService;
use Yii;
use yii\base\Model;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
 * @property bool|null ap_enabled
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
    public $ap_enabled;
    public $ap_hash_code;

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
            [['ap_enabled'], 'boolean'],
            [['ap_subject_json'], 'validateSubject', 'skipOnEmpty' => false, 'skipOnError' => false],
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
            'ap_enabled' => 'Enabled',
            'ap_hash_code' => 'Hash Code',
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

    /**
     * @return string
     */
    public function getDecodeCode(): string
    {
        $code = '';
        $rules = @json_decode($this->ap_subject_json, true);
        if (is_array($rules)) {
            $code = AbacService::conditionDecode($rules, '');
        }
        return $code;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateSubject($attribute, $params)
    {

        //$ap->ap_action_json = \yii\helpers\Json::encode($actionData);
        $code = $this->getDecodeCode();

        if (!$code) {
            $this->addError('ap_subject_json', 'Invalid Expression Language: "' . $code . '"');
        }

//        $expressionLanguage = new ExpressionLanguage();
//
//        $r = new \stdClass();
//        $sub = new \stdClass();
//        $env = new \stdClass();
//        $user = new \stdClass();
//        $user->username = 'test';
//        $env->user = $user;
//        $sub->env = $env;
//        $r->sub = $sub;
//
//        if ($expressionLanguage->evaluate($code, ['r' => $r])) {
//            $this->addError('ap_subject_json', 'Invalid Expression Language: "' . $code . '"');
//        }

        // var_dump($expressionLanguage->evaluate('1 + 2')); // displays 3
        // var_dump($expressionLanguage->compile('1 + 2')); // displays (1 + 2)
    }
}
