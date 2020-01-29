<?php

namespace modules\offer\src\entities\offerViewLog;

use modules\offer\src\entities\offer\Offer;
use yii\db\ActiveQuery;

/**
 * @property int $ofvwl_id
 * @property int $ofvwl_offer_id
 * @property string $ofvwl_visitor_id
 * @property string $ofvwl_ip_address
 * @property string $ofvwl_user_agent
 * @property string $ofvwl_created_dt
 *
 * @property Offer $offer
 */
class OfferViewLog extends \yii\db\ActiveRecord
{
    public static function create(CreateDto $dto): self
    {
        $log = new static();
        $log->ofvwl_offer_id = $dto->ofvwl_offer_id;
        $log->ofvwl_visitor_id = $dto->ofvwl_visitor_id;
        $log->ofvwl_ip_address = $dto->ofvwl_ip_address;
        $log->ofvwl_user_agent = $dto->ofvwl_user_agent;
        $log->ofvwl_created_dt = $dto->ofvwl_created_dt;
        return $log;
    }

    public static function tableName(): string
    {
        return '{{%offer_view_log}}';
    }

    public function rules(): array
    {
        return [
            ['ofvwl_offer_id', 'required'],
            ['ofvwl_offer_id', 'integer'],
            ['ofvwl_offer_id', 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['ofvwl_offer_id' => 'of_id']],

            ['ofvwl_visitor_id', 'string', 'max' => 32],

            ['ofvwl_ip_address', 'string', 'max' => 40],

            ['ofvwl_user_agent', 'string', 'max' => 255],

            ['ofvwl_created_dt', 'required'],
            ['ofvwl_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ofvwl_id' => 'ID',
            'ofvwl_offer_id' => 'Offer ID',
            'offer' => 'Offer',
            'ofvwl_visitor_id' => 'Visitor Id',
            'ofvwl_ip_address' => 'Ip Address',
            'ofvwl_user_agent' => 'User Agent',
            'ofvwl_created_dt' => 'Created Dt',
        ];
    }

    public function getOffer(): ActiveQuery
    {
        return $this->hasOne(Offer::class, ['of_id' => 'ofvwl_offer_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
