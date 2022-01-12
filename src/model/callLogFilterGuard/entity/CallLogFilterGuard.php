<?php

namespace src\model\callLogFilterGuard\entity;

use common\models\Call;
use src\model\contactPhoneList\entity\ContactPhoneList;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "call_log_filter_guard".
 *
 * @property int $clfg_call_id
 * @property int|null $clfg_type
 * @property float|null $clfg_sd_rate
 * @property int|null $clfg_trust_percent
 * @property string|null $clfg_created_dt
 * @property int|null $clfg_cpl_id
 * @property int|null $clfg_call_log_id
 * @property int|null $clfg_redial_status [tinyint(1)]
 */
class CallLogFilterGuard extends \yii\db\ActiveRecord
{
    public const TYPE_TRUST = 0;
    public const TYPE_SPAM = 1;

    public const TYPE_LIST = [
        self::TYPE_TRUST => 'Trust',
        self::TYPE_SPAM => 'Spam'
    ];

    public function rules(): array
    {
        return [
            ['clfg_call_id', 'integer'],
            ['clfg_call_id', 'unique'],

            ['clfg_sd_rate', 'number'],

            ['clfg_trust_percent', 'integer'],

            ['clfg_type', 'integer'],

            ['clfg_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['clfg_cpl_id'], 'integer'],
            [['clfg_cpl_id'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => ContactPhoneList::class, 'targetAttribute' => ['clfg_cpl_id' => 'cpl_id']],

            ['clfg_call_log_id', 'integer'],

            ['clfg_redial_status', 'integer'],
            ['clfg_redial_status', 'in', 'range' => array_keys(Call::STATUS_LIST), 'skipOnEmpty' => true]
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['clfg_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'clfg_call_id' => 'Call ID',
            'clfg_type' => 'Type',
            'clfg_sd_rate' => 'Sd Rate',
            'clfg_trust_percent' => 'Trust Percent',
            'clfg_created_dt' => 'Created DT',
            'clfg_cpl_id' => 'ContactPhoneList ID',
            'clfg_call_log_id' => 'CallLog ID',
            'clfg_redial_status' => 'Call Redial Status',
        ];
    }

    public static function find(): CallLogFilterGuardScopes
    {
        return new CallLogFilterGuardScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'call_log_filter_guard';
    }

    public static function create(
        int $callId,
        int $type,
        ?float $sdRate,
        ?int $trustPercent,
        ?int $contactPhoneListId
    ): CallLogFilterGuard {
        $model = new self();
        $model->clfg_call_id = $callId;
        $model->clfg_type = $type;
        $model->clfg_sd_rate = $sdRate;
        $model->clfg_trust_percent = $trustPercent;
        $model->clfg_cpl_id = $contactPhoneListId;
        return $model;
    }

    public function isSpam(): bool
    {
        return $this->clfg_type === self::TYPE_SPAM;
    }

    public function isTrust(): bool
    {
        return $this->clfg_type === self::TYPE_TRUST;
    }

    public function guardSpam(float $rate): bool
    {
        return $this->isSpam() && $this->clfg_sd_rate > $rate;
    }

    public function guardTrust(float $rate): bool
    {
        return $this->isTrust() && $this->clfg_sd_rate < $rate;
    }

    public function getTypeName(): ?string
    {
        return self::TYPE_LIST[$this->clfg_type] ?? null;
    }

    public function getRedialStatusName(): ?string
    {
        return Call::STATUS_LIST[$this->clfg_redial_status] ?? null;
    }

    public function setRedialStatusByTwilioStatus(string $statusName): void
    {
        $this->clfg_redial_status = Call::getStatusByTwilioStatus($statusName);
    }
}
