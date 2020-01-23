<?php

namespace sales\forms\lead;

use common\models\Currency;
use sales\helpers\lead\LeadPreferencesHelper;
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
	 * LeadPreferencesForm constructor.
	 * @param Lead|null $lead
	 * @param array $config
	 */
	public function __construct(Lead $lead, $config = [])
	{
		if ($lead && $leadPreferences = $lead->leadPreferences) {
			$this->marketPrice = $leadPreferences->market_price;
			$this->clientsBudget = $leadPreferences->clients_budget;
			$this->numberStops = $leadPreferences->number_stops;
            $this->currency = $leadPreferences->pref_currency;

			$this->notesForExperts = $lead->notes_for_experts;
			$this->delayedCharge = $lead->l_delayed_charge;
		}

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
            [['currency'], 'string', 'max' => 3],
            ['currency', 'default', 'value' => null],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],
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
            'currency'  => 'Currency'
		];
	}
}