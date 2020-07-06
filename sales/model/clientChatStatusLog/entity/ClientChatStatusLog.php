<?php

namespace sales\model\clientChatStatusLog\entity;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;

/**
 * This is the model class for table "client_chat_status_log".
 *
 * @property int $csl_id
 * @property int $csl_cch_id
 * @property int|null $csl_from_status
 * @property int|null $csl_to_status
 * @property string|null $csl_start_dt
 * @property string|null $csl_end_dt
 * @property int|null $csl_owner_id
 * @property string|null $csl_description
 *
 * @property ClientChat $cslCch
 * @property Employee $cslOwner
 */
class ClientChatStatusLog extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['csl_cch_id', 'required'],
            ['csl_cch_id', 'integer'],
            ['csl_cch_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['csl_cch_id' => 'cch_id']],

            ['csl_description', 'string', 'max' => 255],

            ['csl_end_dt', 'safe'],

            ['csl_from_status', 'integer'],

            ['csl_owner_id', 'integer'],
            ['csl_owner_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['csl_owner_id' => 'id']],

            ['csl_start_dt', 'safe'],

            ['csl_to_status', 'integer'],
        ];
    }

    public function getCslCch(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'csl_cch_id']);
    }

    public function getCslOwner(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'csl_owner_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'csl_id' => 'ID',
            'csl_cch_id' => 'Client Chat ID',
            'csl_from_status' => 'From Status',
            'csl_to_status' => 'To Status',
            'csl_start_dt' => 'Start Dt',
            'csl_end_dt' => 'End Dt',
            'csl_owner_id' => 'Owner ID',
            'csl_description' => 'Description',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_status_log';
    }
}
