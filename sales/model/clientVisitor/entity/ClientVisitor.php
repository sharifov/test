<?php

namespace sales\model\clientVisitor\entity;

use common\models\Client;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_visitor".
 *
 * @property int $cv_id
 * @property int $cv_client_id
 * @property string $cv_visitor_id
 * @property string|null $cv_created_dt
 *
 * @property Client $cvClient
 */
class ClientVisitor extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cv_client_id', 'required'],
            ['cv_client_id', 'integer'],
            ['cv_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cv_client_id' => 'id']],

            ['cv_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cv_visitor_id', 'required'],
            ['cv_visitor_id', 'string', 'max' => 50],

            [['cv_client_id', 'cv_visitor_id'], 'unique', 'targetAttribute' => ['cv_client_id', 'cv_visitor_id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cv_created_dt', 'cv_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cv_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCvClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'cv_client_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cv_id' => 'ID',
            'cv_client_id' => 'Client',
            'cv_visitor_id' => 'Visitor ID',
            'cv_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): ClientVisitorScopes
    {
        return new ClientVisitorScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_visitor';
    }

    public static function create(int $clientId, string $visitorId): ClientVisitor
    {
        $model = new self();
        $model->cv_client_id = $clientId;
        $model->cv_visitor_id = $visitorId;
        return $model;
    }
}
