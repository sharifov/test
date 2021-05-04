<?php

namespace webapi\src\forms\quote;

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
 * @property $email_from
 * @property $email_from_name
 * @property $email_to
 * @property $additional_data
 *
 * @property Quote $quote
 */
class SendQuoteApiForm extends Model
{
    public $quote_uid;
    public $template_key;
    public $language_id;
    public $market_country_code;
    public $email_from;
    public $email_from_name;
    public $email_to;
    public $additional_data;

    private Quote $quote;

    public function rules(): array
    {
        return [
            [['quote_uid', 'template_key', 'email_from', 'email_to'], 'required'],
            [['quote_uid'], 'string', 'max' => 13],
            [['quote_uid'], 'checkQuote'],

            [['template_key', 'email_from', 'email_from_name', 'email_to'], 'string', 'max' => 50],

            [['market_country_code'], 'string', 'max' => 2],

            [['language_id'], 'string', 'max' => 5],
            [
                ['language_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => ['language_id' => 'language_id']
            ],

            [['email_from', 'email_to'], 'email'],

            [['additional_data'], CheckJsonValidator::class],
            [
                ['additional_data'],
                'filter',
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
