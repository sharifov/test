<?php

namespace modules\flight\models;

use modules\flight\models\FlightRequest;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

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
            [['flr_fr_id'], 'required'],
            [['flr_fr_id', 'flr_status_id_old', 'flr_status_id_new'], 'integer'],
            [['flr_created_dt', 'flr_updated_dt'], 'safe'],
            [['flr_description'], 'string', 'max' => 500],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['flr_created_dt', 'flr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['flr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function getOldStatusName(): ?string
    {
        return FlightRequest::STATUS_LIST[$this->flr_status_id_old] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewStatusName(): ?string
    {
        return FlightRequest::STATUS_LIST[$this->flr_status_id_new] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flr_id' => 'ID',
            'flr_fr_id' => 'Flight Request ID',
            'flr_status_id_old' => 'Status Old',
            'flr_status_id_new' => 'Status New',
            'flr_description' => 'Description',
            'flr_created_dt' => 'Created Dt',
            'flr_updated_dt' => 'Updated Dt',
        ];
    }

    public static function create(
        int $flightRequestId,
        ?int $oldStatus,
        ?int $newStatus,
        ?string $description
    ): FlightRequestLog {
        $model = new self();
        $model->flr_fr_id = $flightRequestId;
        $model->flr_status_id_old = $oldStatus;
        $model->flr_status_id_new = $newStatus;
        $model->flr_description = StringHelper::truncate($description, 499, '');
        return $model;
    }
}
