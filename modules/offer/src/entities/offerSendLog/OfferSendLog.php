<?php

namespace modules\offer\src\entities\offerSendLog;

use common\models\Employee;
use modules\offer\src\entities\offer\Offer;
use yii\db\ActiveQuery;

/**
 * @property int $ofsndl_id
 * @property int $ofsndl_offer_id
 * @property int $ofsndl_type_id
 * @property string $ofsndl_send_to
 * @property int|null $ofsndl_created_user_id
 * @property string $ofsndl_created_dt
 *
 * @property Employee $createdUser
 * @property Offer $offer
 */
class OfferSendLog extends \yii\db\ActiveRecord
{
    public static function create(CreateDto $dto): self
    {
        $log = new static();
        $log->ofsndl_offer_id = $dto->ofsndl_offer_id;
        $log->ofsndl_type_id = $dto->ofsndl_type_id;
        $log->ofsndl_created_user_id = $dto->ofsndl_created_user_id;
        $log->ofsndl_send_to = $dto->ofsndl_send_to;
        $log->ofsndl_created_dt = $dto->ofsndl_created_dt;
        return $log;
    }

    public static function tableName(): string
    {
        return '{{%offer_send_log}}';
    }

    public function rules(): array
    {
        return [
            ['ofsndl_offer_id', 'required'],
            ['ofsndl_offer_id', 'integer'],
            ['ofsndl_offer_id', 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['ofsndl_offer_id' => 'of_id']],

            ['ofsndl_type_id', 'required'],
            ['ofsndl_type_id', 'integer'],
            ['ofsndl_type_id', 'in', 'range' => array_keys(OfferSendLogType::getList())],

            ['ofsndl_send_to', 'string', 'max' => 160],

            ['ofsndl_created_user_id', 'integer'],
            ['ofsndl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ofsndl_created_user_id' => 'id']],

            ['ofsndl_created_dt', 'required'],
            ['ofsndl_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ofsndl_id' => 'ID',
            'ofsndl_offer_id' => 'Offer ID',
            'offer' => 'Offer',
            'ofsndl_type_id' => 'Type',
            'ofsndl_send_to' => 'Send to',
            'ofsndl_created_user_id' => 'Created User',
            'createdUser' => 'Created User',
            'ofsndl_created_dt' => 'Created Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ofsndl_created_user_id']);
    }
    
    public function getOffer(): ActiveQuery
    {
        return $this->hasOne(Offer::class, ['of_id' => 'ofsndl_offer_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
