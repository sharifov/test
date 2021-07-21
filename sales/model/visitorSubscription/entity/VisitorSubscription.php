<?php

namespace sales\model\visitorSubscription\entity;

use sales\entities\EventTrait;
use sales\model\visitorSubscription\event\VisitorSubscriptionEnabled;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "visitor_subscription".
 *
 * @property int $vs_id
 * @property string $vs_subscription_uid
 * @property int $vs_type_id
 * @property int|null $vs_enabled
 * @property string|null $vs_expired_date
 * @property string|null $vs_created_dt
 * @property string|null $vs_updated_dt
 */
class VisitorSubscription extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const SUBSCRIPTION_FLIZZARD = 1;

    public const SUBSCRIPTION_LIST_NAME = [
        self::SUBSCRIPTION_FLIZZARD => 'Flizzard'
    ];

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['vs_created_dt', 'vs_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['vs_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }

    public function rules(): array
    {
        return [
            [['vs_subscription_uid', 'vs_type_id'], 'unique', 'targetAttribute' => ['vs_subscription_uid', 'vs_type_id'], 'message' => 'Subscription uid with type has already been taken'],

            ['vs_created_dt', 'safe'],

            ['vs_enabled', 'integer'],

            ['vs_expired_date', 'safe'],

            ['vs_subscription_uid', 'required'],
            ['vs_subscription_uid', 'string', 'max' => 100],

            ['vs_type_id', 'required'],
            ['vs_type_id', 'integer'],

            ['vs_updated_dt', 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'vs_id' => 'ID',
            'vs_subscription_uid' => 'Subscription Uid',
            'vs_type_id' => 'Type ID',
            'vs_enabled' => 'Enabled',
            'vs_expired_date' => 'Expired Date',
            'vs_created_dt' => 'Created Dt',
            'vs_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'visitor_subscription';
    }

    public static function getSubscriptionListName(): array
    {
        return self::SUBSCRIPTION_LIST_NAME;
    }

    public function getSubscriptionName(): string
    {
        return self::getSubscriptionListName()[$this->vs_type_id] ?? 'Unknown subscription';
    }

    public static function createByApi(string $subscriptionUid, ?string $expiredDate)
    {
        $self = new self();
        $self->vs_subscription_uid = $subscriptionUid;
        $self->vs_expired_date = $expiredDate;
        return $self;
    }

    public function setFlizzardType(): void
    {
        $this->vs_type_id = self::SUBSCRIPTION_FLIZZARD;
    }

    public function enabled(): void
    {
        $this->vs_enabled = 1;
        $this->recordEvent(new VisitorSubscriptionEnabled($this->vs_subscription_uid));
    }

    public function disabled(): void
    {
        $this->vs_enabled = 0;
    }
}
