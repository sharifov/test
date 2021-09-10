<?php

namespace sales\model\callLogFilterGuard\entity;

use Yii;

/**
 * This is the model class for table "call_log_filter_guard".
 *
 * @property int $clfg_call_id
 * @property int|null $clfg_type
 * @property float|null $clfg_sd_rate
 * @property int|null $clfg_trust_percent
 */
class CallLogFilterGuard extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['clfg_sd_rate', 'number'],

            ['clfg_trust_percent', 'integer'],

            ['clfg_type', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'clfg_call_id' => 'Call ID',
            'clfg_type' => 'Type',
            'clfg_sd_rate' => 'Sd Rate',
            'clfg_trust_percent' => 'Trust Percent',
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
        ?int $trustPercent
    ): CallLogFilterGuard {
        $model = new self();
        $model->clfg_call_id = $callId;
        $model->clfg_type = $type;
        $model->clfg_sd_rate = $sdRate;
        $model->clfg_trust_percent = $trustPercent;
        return $model;
    }
}
