<?php

namespace common\models;

use common\models\local\LeadLogMessage;
use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "lead_preferences".
 *
 * @property int $id
 * @property int $lead_id
 * @property string $notes
 * @property string $pref_language
 * @property string $pref_currency
 * @property string $pref_airline
 * @property int $number_stops
 * @property double $clients_budget
 * @property double $market_price
 *
 * @property Lead $lead
 */
class LeadPreferences extends \yii\db\ActiveRecord implements AggregateRoot
{

    use EventTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_preferences';
    }

    public static function create($leadId, $marketPrice, $clientsBudget, $numberStops): self
    {
        $preferences = new static();
        $preferences->lead_id = $leadId;
        $preferences->market_price = $marketPrice;
        $preferences->clients_budget = $clientsBudget;
        $preferences->number_stops = $numberStops;
        return $preferences;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lead_id', 'number_stops'], 'integer'],
            [['notes'], 'string'],
            [['clients_budget', 'market_price'], 'number'],
            [['pref_language', 'pref_currency', 'pref_airline'], 'string', 'max' => 255],
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
    }

    public function beforeValidate()
    {
        $this->clients_budget = floatval($this->clients_budget);
        $this->market_price = floatval($this->market_price);
        $this->number_stops = intval($this->number_stops);

        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //Add logs after changed model attributes
        $leadLog = new LeadLog((new LeadLogMessage()));
        $leadLog->logMessage->oldParams = $changedAttributes;
        $leadLog->logMessage->newParams = array_intersect_key($this->attributes, $changedAttributes);
        $leadLog->logMessage->title = ($insert)
            ? 'Create' : 'Update';
        $leadLog->logMessage->model = $this->formName();
        $leadLog->addLog([
            'lead_id' => $this->lead_id,
        ]);
    }
}
