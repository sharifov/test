<?php

namespace webapi\src\forms\leadRequest;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\appProjectKey\entity\AppProjectKey;
use src\model\leadRequest\service\LeadRequestDictionary;
use src\model\leadRequest\service\LeadRequestService;
use webapi\src\forms\leadRequest\userColumnData\UserColumnDataForm;
use yii\base\Model;

/**
 * Class AdwordsRequestForm
 *
 * @property $google_key
 * @property $user_column_data
 * @property $is_test
 *
 * @property AppProjectKey|null $appProjectKey
 * @property UserColumnDataForm|null $userColumnDataForm
 */
class AdwordsRequestForm extends Model
{
    public $google_key;
    public $user_column_data;
    public $is_test;

    private $appProjectKey;
    private $userColumnDataForm;

    public function rules(): array
    {
        return [
            [['google_key'], 'required'],
            [['google_key'], 'string', 'max' => 50],
            [['google_key'], 'checkGoogleKey'],

            [['user_column_data'], 'required'],
            [['user_column_data'], CheckJsonValidator::class],
            [['user_column_data'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['user_column_data'], 'fillUserColumnData'],

            ['is_test', 'default', 'value' => false],
            ['is_test', 'boolean'],
        ];
    }

    public function checkGoogleKey($attribute): void
    {
        if (!$this->appProjectKey = AppProjectKey::findOne(['apk_key' => $this->google_key])) {
            $this->addError($attribute, 'AppProjectKey not found by apk_key(' . $this->google_key . ')');
        }
    }

    public function fillUserColumnData($attribute): void
    {
        $form = new UserColumnDataForm();
        $form->email = LeadRequestService::findByColumnId(LeadRequestDictionary::COLUMN_EMAIL, $this->user_column_data);
        $form->phone = LeadRequestService::findByColumnId(LeadRequestDictionary::COLUMN_PHONE, $this->user_column_data);

        if (!$form->validate()) {
            $this->addError($attribute, 'UserColumnDataForm: ' . ErrorsToStringHelper::extractFromModel($form, ', '));
        }
        if (!$this->hasErrors($attribute)) {
            $this->userColumnDataForm = $form;
        }
    }

    public function getAppProjectKey(): AppProjectKey
    {
        return $this->appProjectKey;
    }

    public function getUserColumnDataForm(): UserColumnDataForm
    {
        return $this->userColumnDataForm;
    }

    public function formName()
    {
        return '';
    }
}
