<?php

namespace webapi\src\forms\quote;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\validators\CheckJsonValidator;
use common\models\Language;
use common\models\Quote;
use frontend\helpers\JsonHelper;
use yii\base\Model;

/**
 * Class SendQuoteApiForm
 *
 * @property $quote_uid
 * @property $template_key
 * @property $language_id
 * @property $market_country_code
 * @property $sms_from
 * @property $sms_to
 * @property $additional_data
 *
 * @property Quote $quote
 */
class SendSmsQuoteApiForm extends Model
{
    public $quote_uid;
    public $template_key;
    public $language_id;
    public $market_country_code;
    public $sms_from;
    public $sms_to;
    public $additional_data;

    private ?Quote $quote;

    public function rules(): array
    {
        return [
            [['quote_uid', 'template_key', 'sms_from', 'sms_to'], 'required'],
            [['quote_uid'], 'string', 'max' => 13],
            [['quote_uid'], 'checkQuote'],

            [['template_key', 'sms_from', 'sms_to'], 'string', 'max' => 50],

            [['market_country_code'], 'string', 'max' => 2],

            [['language_id'], 'string', 'max' => 5],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['language_id' => 'language_id']],

            [['sms_from', 'sms_to'], PhoneInputValidator::class],

            [['additional_data'], CheckJsonValidator::class],
            [['additional_data'], 'filter',
                'filter' => static function ($value) {
                    if (!$value) {
                        return [];
                    }
                    return JsonHelper::decode($value);
                }
            ],
        ];
    }

    public function checkQuote($attribute)
    {
        if (!$this->quote = Quote::findOne(['uid' => $this->quote_uid])) {
            $this->addError($attribute, 'Quote not found by Uid(' . $this->quote_uid . ')');
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }
}
