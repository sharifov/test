<?php

namespace modules\attraction\models\forms;

use yii\base\Model;
use yii\base\DynamicModel;
use common\components\validators\PhoneValidator;
use borales\extensions\phoneInput\PhoneInputValidator;

class BookingAnswersForm extends DynamicModel
{
    public string $bookingId = '';
    public string $quoteId = '';
    public string $leadPassengerName = '';

    public function rules()
    {
        return [
            ['bookingId', 'required'],
            ['quoteId', 'required'],
            ['leadPassengerName', 'required']
        ];
    }

    public function initDynamicFields(array $bookingData)
    {
        $qList = !empty($bookingData['questionList']['nodes']) ? $bookingData['questionList']['nodes'] : [];
        $qListAdditional = !empty($bookingData['availabilityList']['nodes'][0]['questionList']['nodes']) ? $bookingData['availabilityList']['nodes'][0]['questionList']['nodes'] : [];

        foreach ($qList as $item) {
            $this->defineAttribute(strtolower(str_replace(' ', '_', $item['label'])));
            $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), $item['isRequired'] ? 'required' : 'safe');
            if ($item['dataFormat'] === 'EMAIL_ADDRESS') {
                $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), 'email');
            }

            if ($item['dataFormat'] === 'PHONE_NUMBER') {
                //$this->addRule(strtolower(str_replace(' ', '_', $item['label'])), PhoneValidator::class, ['skipOnEmpty' => true]);
                $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), PhoneInputValidator::class);
                $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), 'string', ['max' => 20]);
            }
        }

        foreach ($qListAdditional as $item) {
            $this->defineAttribute(strtolower(str_replace(' ', '_', $item['label'])));
            $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), $item['isRequired'] ? 'required' : 'safe');
            if ($item['dataFormat'] === 'EMAIL_ADDRESS') {
                $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), 'email');
            }

            if ($item['dataFormat'] === 'PHONE_NUMBER') {
                //$this->addRule(strtolower(str_replace(' ', '_', $item['label'])), PhoneValidator::class, ['skipOnEmpty' => true]);
                $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), PhoneInputValidator::class);
                $this->addRule(strtolower(str_replace(' ', '_', $item['label'])), 'string', ['max' => 20]);
            }
        }
    }
}
