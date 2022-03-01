<?php

namespace src\forms\lead;

use common\models\Currency;
use common\models\Language;
use src\helpers\lead\LeadPreferencesHelper;
use yii\base\Model;
use common\models\Lead;

class LeadPreferencesForm extends Model
{
    /**
     * @var int
     */
    public $marketPrice;

    /**
     * @var int
     */
    public $clientsBudget;

    /**
     * @var int
     */
    public $numberStops;

    /**
     * @var string
     */
    public $notesForExperts;

    /**
     * @var int
     */
    public $delayedCharge = 0;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string|null
     */
    public $clientLang;

    /**
     * @var bool
     */
    public bool $canManageCurrency = true;

    /**
     * LeadPreferencesForm constructor.
     * @param Lead|null $lead
     * @param bool $canManageCurrency
     * @param array $config
     */
    public function __construct(Lead $lead, bool $canManageCurrency, $config = [])
    {
        if ($lead && $leadPreferences = $lead->leadPreferences) {
            $this->marketPrice = $leadPreferences->market_price;
            $this->clientsBudget = $leadPreferences->clients_budget;
            $this->numberStops = $leadPreferences->number_stops;
            $this->currency = $leadPreferences->pref_currency;

            $this->notesForExperts = $lead->notes_for_experts;
            $this->delayedCharge = $lead->l_delayed_charge;
            $this->clientLang = $lead->l_client_lang;
        }
        $this->canManageCurrency = $canManageCurrency;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['marketPrice'], 'number'],
            [['clientsBudget'], 'number'],
            ['numberStops', 'integer'],
            ['numberStops', 'in', 'range' => array_keys(LeadPreferencesHelper::listNumberStops())],
            ['notesForExperts', 'string'],
            ['delayedCharge', 'boolean'],
            ['delayedCharge', 'default', 'value' => false],
            [['numberStops'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['marketPrice', 'clientsBudget'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
            [['currency'], 'required', 'when' => function (): bool {
                return $this->canManageCurrency;
            }],
            [['currency'], 'string', 'max' => 3],
            ['currency', 'default', 'value' => null],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],

            [['clientLang'], 'filter', 'filter' => static function ($value) {
                return $value === '' ? null : $value;
            }],
            [['clientLang'], 'string', 'max' => 5],
            ['clientLang', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true,
                'targetClass' => Language::class, 'targetAttribute' => ['clientLang' => 'language_id']],
        ];
    }



    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'marketPrice' => 'Market Price',
            'clientsBudget' => 'Client Budget',
            'numberStops' => 'Number Stops',
            'notesForExperts' => 'Notes for Expert',
            'delayedCharge' => 'Delayed charge',
            'currency'  => 'Currency',
            'clientLang' => 'Client Lang',
        ];
    }
}
