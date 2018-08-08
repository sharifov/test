<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "leads".
 *
 * @property int $id
 * @property int $client_id
 * @property int $employee_id
 * @property int $status
 * @property string $uid
 * @property int $project_id
 * @property int $source_id
 * @property string $trip_type
 * @property string $cabin
 * @property int $adults
 * @property int $children
 * @property int $infants
 * @property string $notes_for_experts
 * @property string $created
 * @property string $updated
 *
 * @property LeadFlightSegment[] $leadFlightSegments
 * @property LeadPreferences $leadPreferences
 * @property Client $client
 * @property Employee $employee
 * @property Source $source
 * @property Project $project
 */
class Lead extends \yii\db\ActiveRecord
{
    const
        TYPE_ONE_WAY = 'OW',
        TYPE_ROUND_TRIP = 'RT',
        TYPE_MULTI_DESTINATION = 'MC';

    const
        STATUS_PENDING = 1,
        STATUS_PROCESSING = 2,
        STATUS_BOOKED = 3,
        STATUS_SOLD = 4,
        STATUS_FOLLOW_UP = 5,
        STATUS_HOLD_ON = 6,
        STATUS_TRASH = 7;

    const
        CABIN_ECONOMY = 'E',
        CABIN_BUSINESS = 'B',
        CABIN_FIRST = 'F',
        CABIN_PREMIUM = 'P';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'employee_id', 'status', 'project_id', 'source_id'], 'integer'],
            [['trip_type', 'cabin', 'updated', 'adults', 'children', 'infants', 'source_id'], 'required'],
            [['adults', 'children', 'infants'], 'integer', 'max' => 9],
            [['adults'], 'integer', 'min' => 1],
            [['notes_for_experts'], 'string'],
            [['created', 'updated'], 'safe'],
            [['uid'], 'string', 'max' => 255],
            [['trip_type'], 'string', 'max' => 2],
            [['cabin'], 'string', 'max' => 1],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'employee_id' => 'Employee ID',
            'status' => 'Status',
            'uid' => 'Uid',
            'project_id' => 'Project ID',
            'source_id' => 'Source ID',
            'trip_type' => 'Trip Type',
            'cabin' => 'Cabin',
            'adults' => 'Adults',
            'children' => 'Children',
            'infants' => 'Infants',
            'notes_for_experts' => 'Notes For Experts',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlightSegments()
    {
        return $this->hasMany(LeadFlightSegment::className(), ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadPreferences()
    {
        return $this->hasOne(LeadPreferences::className(), ['lead_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public static function getLeadQueueType()
    {
        return [
            'inbox', 'follow-up', 'processing',
            'processing-all', 'booked', 'sold', 'trash'
        ];
    }

    public static function getBadges()
    {
        $badges = array_flip(self::getLeadQueueType());
        foreach ($badges as $key => $value) {
            $badges[$key] = 0;
        }

        return $badges;
    }

    public static function getFlightType($flightType = null)
    {
        $mapping = [
            self::TYPE_ROUND_TRIP => 'Round Trip',
            self::TYPE_ONE_WAY => 'One Way',
            self::TYPE_MULTI_DESTINATION => 'Multidestination'
        ];

        if ($flightType === null) {
            return $mapping;
        }

        return isset($mapping[$flightType]) ? $mapping[$flightType] : $flightType;
    }

    public static function getCabin($cabin = null)
    {
        $mapping = [
            self::CABIN_ECONOMY => 'Economy',
            self::CABIN_PREMIUM => 'Premium eco',
            self::CABIN_BUSINESS => 'Business',
            self::CABIN_FIRST => 'First',
        ];

        if ($cabin === null) {
            return $mapping;
        }

        return isset($mapping[$cabin]) ? $mapping[$cabin] : $cabin;
    }

    public function beforeValidate()
    {
        $this->updated = date('Y-m-d H:i:s');
        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        if ($this->isNewRecord && !empty($this->source_id)) {
            $source = Source::findOne(['id' => $this->source_id]);
            if ($source !== null) {
                $this->project_id = $source->project_id;
            }
        }

        parent::afterValidate();
    }

    public function getPaxTypes()
    {
        $types = [];
        for ($i = 0; $i < $this->adults; $i++) {
            $types[] = QuotePrice::PASSENGER_ADULT;
        }
        for ($i = 0; $i < $this->children; $i++) {
            $types[] = QuotePrice::PASSENGER_CHILD;
        }
        for ($i = 0; $i < $this->infants; $i++) {
            $types[] = QuotePrice::PASSENGER_INFANT;
        }

        return $types;
    }
}
