<?php

namespace common\models;

use common\models\local\LeadLogMessage;
use Yii;

/**
 * This is the model class for table "lead_flight_segments".
 *
 * @property int $id
 * @property int $lead_id
 * @property string $origin
 * @property string $destination
 * @property string $departure
 * @property int $flexibility
 * @property string $flexibility_type
 * @property string $created
 * @property string $updated
 * @property string $origin_label
 * @property string $destination_label
 *
 * @property Lead $lead
 */
class LeadFlightSegment extends \yii\db\ActiveRecord
{


    public CONST FLEX_TYPE_MINUS = '-';
    public CONST FLEX_TYPE_PLUS = '+';
    public CONST FLEX_TYPE_PLUS_MINUS = '+/-';

    public CONST FLEX_TYPE_LIST = [
        self::FLEX_TYPE_MINUS => '-',
        self::FLEX_TYPE_PLUS => '+',
        self::FLEX_TYPE_PLUS_MINUS => '+/-',
    ];


    public CONST SCENARIO_CREATE_AGENT = 'create_agent';
    public CONST SCENARIO_CREATE_API = 'create_api';
    public CONST SCENARIO_UPDATE_API = 'update_api';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_flight_segments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['departure'], 'required'],

            [['origin', 'destination'], 'required', 'on' => [self::SCENARIO_CREATE_API, self::SCENARIO_UPDATE_API]],

            [['origin'], 'checkOriginIata', 'on' => [self::SCENARIO_CREATE_API, self::SCENARIO_UPDATE_API]],
            [['destination'], 'checkDestinationIata', 'on' => [self::SCENARIO_CREATE_API, self::SCENARIO_UPDATE_API]],

            [['origin_label', 'destination_label'], 'required', 'except' => [self::SCENARIO_CREATE_API, self::SCENARIO_UPDATE_API]],

            [['origin', 'destination'], 'string', 'max' => 3],
            [['lead_id', 'flexibility'], 'integer'],

            [['flexibility_type'], 'default', 'value' => '+/-', 'on' => 'insert'],
            [['origin_label', 'destination_label'], 'trim'],

            [['origin_label'], 'checkOriginLabel'],
            [['destination_label'], 'checkDestinationLabel'],

            [['departure', 'created', 'updated', 'flexibility_type', 'flexibility', 'origin_label', 'destination_label'], 'safe'],

            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
        ];
    }


    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        //$scenarios[self::SCENARIO_CREATE_AGENT] = ['origin', 'destination', 'departure', 'origin_label', 'destination_label', 'lead_id', 'flexibility', 'flexibility_type'];
        $scenarios[self::SCENARIO_CREATE_API] = ['origin', 'destination', 'departure', 'lead_id', 'flexibility', 'flexibility_type'];
        $scenarios[self::SCENARIO_UPDATE_API] = ['origin', 'destination', 'departure', 'lead_id', 'flexibility', 'flexibility_type'];
        return $scenarios;
    }



    public function checkOriginIata() : void
    {
        $origin = Airport::findIdentity($this->origin);
        if ($origin) {
            $this->origin_label = sprintf('%s (%s)', $origin->city, $origin->iata);
        } else {
            $this->addError('origin', sprintf('Not found %s IATA ("'.$this->origin.'") ',
                $this->getAttributeLabel('origin')
            ));
        }
    }

    public function checkDestinationIata() : void
    {
        $destination = Airport::findIdentity($this->destination);
        if ($destination) {
            $this->destination_label = sprintf('%s (%s)', $destination->city, $destination->iata);
        } else {
            $this->addError('origin', sprintf('Not found %s IATA ("'.$this->destination.'") ',
                $this->getAttributeLabel('destination')
            ));
        }
    }


    public function checkOriginLabel() : void
    {
        $this->origin_label = trim($this->origin_label);
        if (!empty($this->origin_label)) {
            $regex = '/(.*)[(]+[A-Z]{3}+[)]$/';
            $hits = preg_match_all($regex, $this->origin_label, $matches, PREG_PATTERN_ORDER);
            if ($hits) {
                $iata = str_replace('(', '', str_replace($matches[1][0], '', $matches[0][0]));
                $this->origin = str_replace(')', '', $iata);
            } else {
                $this->addError('origin_label', sprintf('%s invalid format.',
                    $this->getAttributeLabel('origin_label')
                ));
            }
        } else {
            $origin = Airport::findIdentity($this->origin);
            if ($origin !== null) {
                $this->origin_label = sprintf('%s (%s)', $origin->city, $origin->iata);
            } else {
                $this->addError('origin_label', sprintf('%s cannot be blank.',
                    $this->getAttributeLabel('origin_label')
                ));
            }
        }
    }

    public function checkDestinationLabel() : void
    {
        $this->destination_label = trim($this->destination_label);
        if (!empty($this->destination_label)) {
            $regex = '/(.*)[(]+[A-Z]{3}+[)]$/';
            $hits = preg_match_all($regex, $this->destination_label, $matches, PREG_PATTERN_ORDER);
            if ($hits) {
                $iata = str_replace('(', '', str_replace($matches[1][0], '', $matches[0][0]));
                $this->destination = str_replace(')', '', $iata);
            } else {
                $this->addError('destination_label', sprintf('%s invalid format.',
                    $this->getAttributeLabel('destination_label')
                ));
            }
        } else {
            $destination = Airport::findIdentity($this->destination);
            if ($destination !== null) {
                $this->destination_label = sprintf('%s (%s)', $destination->city, $destination->iata);
            } else {
                $this->addError('destination_label', sprintf('%s cannot be blank.',
                    $this->getAttributeLabel('destination_label')
                ));
            }
        }
    }


    /*public function beforeValidate()
    {
        $this->origin_label = trim($this->origin_label);
        if (!empty($this->origin_label)) {
            $regex = '/(.*)[(]+[A-Z]{3}+[)]$/';
            $hits = preg_match_all($regex, $this->origin_label, $matches, PREG_PATTERN_ORDER);
            if ($hits) {
                $iata = str_replace('(', '', str_replace($matches[1][0], '', $matches[0][0]));
                $this->origin = str_replace(')', '', $iata);
            } else {
                $this->addError('origin_label', sprintf('%s invalid format.',
                    $this->getAttributeLabel('origin_label')
                ));
            }
        } else {
            $origin = Airport::findIdentity($this->origin);
            if ($origin !== null) {
                $this->origin_label = sprintf('%s (%s)', $origin->name, $origin->iata);
            } else {
                $this->addError('origin_label', sprintf('%s cannot be blank.',
                    $this->getAttributeLabel('origin_label')
                ));
            }
        }

        $this->destination_label = trim($this->destination_label);
        if (!empty($this->destination_label)) {
            $regex = '/(.*)[(]+[A-Z]{3}+[)]$/';
            $hits = preg_match_all($regex, $this->destination_label, $matches, PREG_PATTERN_ORDER);
            if ($hits) {
                $iata = str_replace('(', '', str_replace($matches[1][0], '', $matches[0][0]));
                $this->destination = str_replace(')', '', $iata);
            } else {
                $this->addError('destination_label', sprintf('%s invalid format.',
                    $this->getAttributeLabel('destination_label')
                ));
            }
        } else {
            $destination = Airport::findIdentity($this->destination);
            if ($destination !== null) {
                $this->destination_label = sprintf('%s (%s)', $destination->name, $destination->iata);
            } else {
                $this->addError('destination_label', sprintf('%s cannot be blank.',
                    $this->getAttributeLabel('destination_label')
                ));
            }
        }

        return parent::beforeValidate();
    }*/

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {

            $this->updated = date('Y-m-d H:i:s');

            if ($this->departure) {
                $this->departure = date('Y-m-d', strtotime($this->departure));
            }


            /*if(!$this->origin_label && $this->origin) {
                $origin = Airport::findIdentity($this->origin);
                if ($origin) {
                    $this->origin_label = sprintf('%s (%s)', $origin->name, $origin->iata);
                }
            }

            if(!$this->destination_label && $this->destination) {
                $destination = Airport::findIdentity($this->destination);
                if ($destination) {
                    $this->destination_label = sprintf('%s (%s)', $destination->name, $destination->iata);
                }
            }*/


            $this->flexibility = (int)$this->flexibility;

            return true;
        }
        return false;
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            $resetCallExpert = false;
            if (isset($changedAttributes['origin']) && $changedAttributes['origin'] != $this->origin) {
                $resetCallExpert = true;
            }
            if (isset($changedAttributes['destination']) && $changedAttributes['destination'] != $this->destination) {
                $resetCallExpert = true;
            }
            if (isset($changedAttributes['departure']) && $changedAttributes['departure'] != $this->departure) {
                $resetCallExpert = true;
            }
            if ($resetCallExpert) {
                Yii::$app->db->createCommand('UPDATE ' . Lead::tableName() . ' SET called_expert = :called_expert WHERE id = :id', [
                    ':called_expert' => false,
                    ':id' => $this->lead_id
                ])->execute();
            }
        }

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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lead_id' => 'Lead',
            'origin' => 'Origin',
            'destination' => 'Destination',
            'origin_label' => 'Origin Label',
            'destination_label' => 'Destination Label',
            'departure' => 'Departure',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
    }

}
