<?php

namespace modules\webEngage\form;

use common\components\validators\IsArrayValidator;
use modules\webEngage\settings\WebEngageDictionary;
use sales\traits\FormNameModelTrait;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class WebEngageEventForm
 */
class WebEngageEventForm extends Model
{
    use FormNameModelTrait;

    public $eventName;
    public $userId;
    public $anonymousId;
    public $eventTime;
    public $eventData;

    public function rules(): array
    {
        return [
             [['eventName'], 'required'],
             [['eventName'], 'string', 'max' => 100],
             [['eventName'], 'in', 'range' => array_keys(WebEngageDictionary::EVENT_LIST)],

             ['userId', 'required', 'when' => function ($model) {
                 return empty($model->anonymousId);
             }],
             [['userId'], 'string', 'max' => 100],

             ['anonymousId', 'required', 'when' => function ($model) {
                 return empty($model->userId);
             }],
             [['anonymousId'], 'string', 'max' => 100],

             [['eventTime'], 'datetime', 'format' => 'php:Y-m-d\TH:i:sO', 'skipOnError' => true, 'skipOnEmpty' => true],

             [['eventData'], IsArrayValidator::class, 'skipOnError' => true, 'skipOnEmpty' => true],
             [['eventData'], 'checkIsAssociative'],
         ];
    }

    public function checkIsAssociative(string $attribute): void
    {
        if (!ArrayHelper::isAssociative($this->eventData)) {
            $this->addError($attribute, 'EventData attributes must be associative array');
        }
    }

    public function formName(): string
    {
        return '';
    }
}
