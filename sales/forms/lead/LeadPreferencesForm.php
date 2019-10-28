<?php

namespace sales\forms\lead;

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
			[['marketPrice', 'clientsBudget'], 'number', 'min' => 500, 'max' => 99000],
			['numberStops', 'integer'],
			['numberStops', 'in', 'range' => array_keys(LeadPreferencesHelper::listNumberStops())],
			['notesForExperts', 'string'],
			['delayedCharge', 'boolean'],
			['delayedCharge', 'default', 'value' => false],
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
		];
	}
}