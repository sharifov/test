<?php

namespace modules\flight\models;

use Yii;

/**
 * This is the model class for table "flight_request_log".
 *
 * @property int $flr_id
 * @property int|null $flr_fr_id
 * @property int|null $flr_status_id_old
 * @property int|null $flr_status_id_new
 * @property string|null $flr_description
 * @property string|null $flr_created_dt
 * @property string|null $flr_updated_dt
 */
class FlightRequestLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_request_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flr_fr_id', 'flr_status_id_old', 'flr_status_id_new'], 'integer'],
            [['flr_created_dt', 'flr_updated_dt'], 'safe'],
            [['flr_description'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flr_id' => 'ID',
            'flr_fr_id' => 'Flr Fr ID',
            'flr_status_id_old' => 'Status Id Old',
            'flr_status_id_new' => 'Status Id New',
            'flr_description' => 'Description',
            'flr_created_dt' => 'Created Dt',
            'flr_updated_dt' => 'Updated Dt',
        ];
    }
}
