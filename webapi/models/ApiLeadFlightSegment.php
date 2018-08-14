<?php

namespace webapi\models;

use common\models\Lead;
use Yii;

/**
 * This is the model class "lead_flight_segments".
 *
 * @property string $origin
 * @property string $destination
 * @property string $departure
 * @property int $flexibility
 * @property string $flexibility_type
 * @property string $origin_label
 * @property string $destination_label
 *
 * @property Lead $lead
 */
class ApiLeadFlightSegment extends \yii\db\ActiveRecord
{

    public $origin;
    public $destination;
    public $departure;
    public $flexibility;
    public $flexibility_type;
    public $origin_label;
    public $destination_label;


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
            [['origin', 'destination', 'departure'], 'required'],
            [['flexibility_type', 'flexibility', 'origin_label', 'destination_label'], 'safe'],
            [['origin_label', 'destination_label'], 'trim'],
            [['origin', 'destination'], 'string', 'max' => 3],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

            'lead_id' => 'Lead ID',
            'origin' => 'Origin',
            'destination' => 'Destination',
            'origin_label' => 'Origin label',
            'destination_label' => 'Destination label',
            'departure' => 'Departure',
        ];
    }

}
