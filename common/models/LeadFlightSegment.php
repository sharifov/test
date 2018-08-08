<?php

namespace common\models;

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
            [['lead_id'], 'integer'],
            [['origin', 'destination', 'departure'], 'required'],
            [['departure', 'created', 'updated', 'flexibility_type', 'flexibility', 'origin_label', 'destination_label'], 'safe'],
            [['origin_label', 'destination_label'], 'trim'],
            [['origin', 'destination'], 'string', 'max' => 3],
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::className(), 'targetAttribute' => ['lead_id' => 'id']],
        ];
    }

    public function init()
    {
        if ($this->isNewRecord) {
            $this->flexibility_type = '+/-';
        }

        parent::init();
    }

    public function beforeValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        if (empty($this->origin)) {
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
                $this->addError('origin_label', sprintf('%s cannot be blank.',
                    $this->getAttributeLabel('origin_label')
                ));
            }
        }

        if (empty($this->destination)) {
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
                $this->addError('destination_label', sprintf('%s cannot be blank.',
                    $this->getAttributeLabel('destination_label')
                ));
            }
        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lead_id' => 'Lead ID',
            'origin' => 'Origin',
            'destination' => 'Destination',
            'origin_label' => 'Origin',
            'destination_label' => 'Destination',
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
        return $this->hasOne(Lead::className(), ['id' => 'lead_id']);
    }

    public function afterValidate()
    {
        $this->departure = date('Y-m-d', strtotime($this->departure));

        parent::afterValidate();
    }
}
