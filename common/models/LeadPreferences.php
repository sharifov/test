<?php

namespace common\models;

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
class LeadPreferences extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_preferences';
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
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::className(), 'targetAttribute' => ['lead_id' => 'id']],
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
        return $this->hasOne(Lead::className(), ['id' => 'lead_id']);
    }
}
