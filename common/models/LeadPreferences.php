<?php

namespace common\models;

use common\models\local\LeadLogMessage;
use src\entities\EventTrait;
use src\events\lead\LeadPreferencesUpdateCurrencyEvent;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "lead_preferences".
 *
 * @property int $id
  * @property int|null $lead_id
 * @property string|null $notes
 * @property string|null $pref_language
 * @property string|null $pref_airline
 * @property int|null $number_stops
 * @property float|null $clients_budget
 * @property float|null $market_price
 * @property string|null $pref_currency
 *
 * @property Currency $prefCurrency
 * @property Lead $lead
 */
class LeadPreferences extends \yii\db\ActiveRecord
{
    use EventTrait;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'lead_preferences';
    }

    public static function create($leadId, $marketPrice, $clientsBudget, $numberStops, $currency): self
    {
        $preferences = new static();
        $preferences->lead_id = $leadId;
        $preferences->market_price = $marketPrice;
        $preferences->clients_budget = $clientsBudget;
        $preferences->number_stops = $numberStops;
        $preferences->pref_currency = $currency;

        return $preferences;
    }

    /**
     * @param int $marketPrice
     * @param int $clientBudget
     * @param int $numberStops
     * @param null|string $currency
     *
     */
    public function edit($marketPrice, $clientBudget, $numberStops, $currency): void
    {
        $this->market_price = $marketPrice;
        $this->clients_budget = $clientBudget;
        $this->number_stops = $numberStops;

        /*if ($this->pref_currency != $currency) {
            $this->recordEvent((new LeadPreferencesUpdateCurrencyEvent($this)));
        }*/
        $this->pref_currency = $currency;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['lead_id', 'number_stops'], 'integer'],
            [['lead_id', 'numberStops'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['marketPrice', 'clientsBudget'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
            [['pref_currency'], 'string', 'max' => 3],
            [['notes'], 'string'],
            [['clients_budget', 'market_price'], 'number'],
            [['pref_language', 'pref_airline'], 'string', 'max' => 255],
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
            [['pref_currency'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pref_currency' => 'cur_code']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'lead_id' => 'Lead ID',
            'notes' => 'Notes',
            'pref_language' => 'Pref Language',
            'pref_currency' => 'Pref Currency',
            'pref_airline' => 'Pref Airline',
            'number_stops' => 'Number Stops',
            'clients_budget' => 'Clients Budget',
            'market_price' => 'Market Price',

        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getPrefCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pref_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        $this->clients_budget = (float)$this->clients_budget;
        $this->market_price = (float)$this->market_price;
        $this->number_stops = (int)$this->number_stops;

        return parent::beforeValidate();
    }

    public function setDefaultCurrencyCodeIfNotSet(bool $defaultCurrencyByDb): void
    {
        if (empty($this->pref_currency)) {
            $this->pref_currency = $defaultCurrencyByDb ? Currency::getDefaultCurrencyCodeByDb() : Currency::getDefaultCurrencyCode();
        }
    }

      public function createClone($leadId, $currency): self
    {
        $clone = self::create($leadId, null, null, null, $currency);
        $clone->attributes    = $this->attributes;
        $clone->lead_id = $leadId;
        $clone->pref_currency = $currency;
        $clone->clients_budget = null;
        $clone->market_price = null;
        $clone->number_stops = null;
        $clone->notes = null;
        $clone->pref_airline = null;
        return $clone;
    }
    
    
}
