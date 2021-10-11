<?php

namespace modules\abac\src\forms;

use modules\abac\src\AbacService;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * This is the AbacPolicyImportForm class for table "abac_policy".
 *
 * @property UploadedFile $importFile
 */
class AbacPolicyImportForm extends Model
{
    public $importFile;

    public function rules()
    {
        return [
            [['importFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'json'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $fileName = $this->importFile->baseName . '.' . $this->importFile->extension;
            $filePath = Yii::getAlias('@runtime/' . $fileName);
            if ($this->importFile->saveAs($filePath)) {
                return $filePath;
            }
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'importFile' => 'Import File',
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
