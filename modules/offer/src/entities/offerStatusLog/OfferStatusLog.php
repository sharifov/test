<?php

namespace modules\offer\src\entities\offerStatusLog;

use common\models\Employee;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferStatus;
use yii\db\ActiveQuery;

/**
 * @property int $osl_id
 * @property int $osl_offer_id
 * @property int|null $osl_start_status_id
 * @property int $osl_end_status_id
 * @property string $osl_start_dt
 * @property string|null $osl_end_dt
 * @property int|null $osl_duration
 * @property string|null $osl_description
 * @property int|null $osl_owner_user_id
 * @property int|null $osl_created_user_id
 *
 * @property Employee $createdUser
 * @property Employee $ownerUser
 * @property Offer $offer
 */
class OfferStatusLog extends \yii\db\ActiveRecord
{
    public static function create(CreateDto $dto): self
    {
        $log = new static();
        $log->osl_offer_id = $dto->offerId;
        $log->osl_start_status_id = $dto->startStatusId;
        $log->osl_end_status_id = $dto->endStatusId;
        $log->osl_start_dt = date('Y-m-d H:i:s');
        $log->osl_description = $dto->description;
        $log->osl_owner_user_id = $dto->ownerId;
        $log->osl_created_user_id = $dto->creatorId;
        return $log;
    }

    public function end(): void
    {
        $this->osl_end_dt = date('Y-m-d H:i:s');
        $this->osl_duration = (int) (strtotime($this->osl_end_dt) - strtotime($this->osl_start_dt));
    }

    public static function tableName(): string
    {
        return '{{%offer_status_log}}';
    }

    public function rules(): array
    {
        return [
            ['osl_offer_id', 'required'],
            ['osl_offer_id', 'integer'],
            ['osl_offer_id', 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['osl_offer_id' => 'of_id']],

            ['osl_start_status_id', 'integer'],
            ['osl_start_status_id', 'in', 'range' => array_keys(OfferStatus::getList())],

            ['osl_end_status_id', 'required'],
            ['osl_end_status_id', 'integer'],
            ['osl_end_status_id', 'in', 'range' => array_keys(OfferStatus::getList())],

            ['osl_start_dt', 'required'],
            ['osl_start_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['osl_end_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['osl_duration', 'integer'],

            ['osl_description', 'string', 'max' => 255],

            ['osl_owner_user_id', 'integer'],
            ['osl_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['osl_owner_user_id' => 'id']],

            ['osl_created_user_id', 'integer'],
            ['osl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['osl_created_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'osl_id' => 'ID',
            'osl_offer_id' => 'Offer ID',
            'offer' => 'Offer',
            'osl_start_status_id' => 'Start Status',
            'osl_end_status_id' => 'End Status',
            'osl_start_dt' => 'Start Dt',
            'osl_end_dt' => 'End Dt',
            'osl_duration' => 'Duration',
            'osl_description' => 'Description',
            'osl_owner_user_id' => 'Owner User',
            'osl_created_user_id' => 'Created User',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'osl_created_user_id']);
    }

    public function getOwnerUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'osl_owner_user_id']);
    }

    public function getOffer(): ActiveQuery
    {
        return $this->hasOne(Offer::class, ['of_id' => 'osl_offer_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
