<?php

namespace sales\model\clientChatHold\entity;

use DateTime;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_hold".
 *
 * @property int $cchd_id
 * @property int $cchd_cch_id
 * @property int|null $cchd_cch_status_log_id
 * @property string $cchd_deadline_dt
 * @property string $cchd_start_dt
 *
 * @property ClientChat $clientChat
 * @property ClientChatStatusLog $clientChatStatusLog
 */
class ClientChatHold extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%client_chat_hold}}';
    }

    public function rules(): array
    {
        return [
            [['cchd_cch_id', 'cchd_cch_status_log_id', 'cchd_deadline_dt', 'cchd_start_dt'], 'required'],
            [['cchd_cch_id', 'cchd_cch_status_log_id'], 'integer'],
            [['cchd_deadline_dt', 'cchd_start_dt'], 'datetime'],
            [['cchd_cch_status_log_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChatStatusLog::class, 'targetAttribute' => ['cchd_cch_status_log_id' => 'csl_id']],
            [['cchd_cch_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['cchd_cch_id' => 'cch_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cchd_id' => 'ID',
            'cchd_cch_id' => 'Client Chat ID',
            'cchd_cch_status_log_id' => 'Chat Status Log ID',
            'cchd_deadline_dt' => 'Deadline Dt',
            'cchd_start_dt' => 'Start DT'
        ];
    }

    public function getClientChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'cchd_cch_id']);
    }

    public function getClientChatStatusLog(): ActiveQuery
    {
        return $this->hasOne(ClientChatStatusLog::class, ['csl_id' => 'cchd_cch_status_log_id']);
    }

    public static function find(): ClientChatHoldScopes
    {
        return new ClientChatHoldScopes(static::class);
    }

    /**
     * @param int $clientChatId
     * @param int $statusLogId
     * @param string $deadlineDt
     * @param string $startDt
     * @return ClientChatHold
     */
    public static function create(int $clientChatId, int $statusLogId, string $deadlineDt, string $startDt): ClientChatHold
    {
        if (!$model = self::findOne(['cchd_cch_id' => $clientChatId])) {
            $model = new static();
        }
        $model->cchd_cch_id = $clientChatId;
        $model->cchd_cch_status_log_id = $statusLogId;
        $model->cchd_deadline_dt = $deadlineDt;
        $model->cchd_start_dt = $startDt;
        return $model;
    }

    public static function getStartDT(): string
    {
        return (new DateTime('now'))->format('Y-m-d H:i:s');
    }

    public static function convertDeadlineDTFromMinute(int $minute): string
    {
        return (new DateTime('now'))->modify('+' . $minute . ' minutes')->format('Y-m-d H:i:s');
    }

    public function deadlineStartDiffInSeconds(): int
    {
        $deadLineTsp = (new DateTime($this->cchd_deadline_dt))->getTimestamp();
        $startTsp = (new DateTime($this->cchd_start_dt))->getTimestamp();
        return $deadLineTsp - $startTsp;
    }

    public function deadlineNowDiffInSeconds(): int
    {
        $deadLineTsp = (new DateTime($this->cchd_deadline_dt))->getTimestamp();
        $nowTsp = (new DateTime('now'))->getTimestamp();
        return $deadLineTsp - $nowTsp;
    }

    public function nowStartDiffSeconds(): int
    {
        $startTsp = (new DateTime($this->cchd_start_dt))->getTimestamp();
        $nowTsp = (new DateTime('now'))->getTimestamp();
        return $nowTsp - $startTsp;
    }

    public function halfWarningSeconds(): int
    {
        return $this->deadlineStartDiffInSeconds() / 2;
    }

    public function isDead(): bool
    {
        return ($this->deadlineNowDiffInSeconds() <= 0);
    }

    public function intervalStartDeadline(string $format = '%i'): string
    {
        $deadlineDt = new DateTime($this->cchd_deadline_dt);
        $startDt = new DateTime($this->cchd_start_dt);
        $interval = $deadlineDt->diff($startDt);
        return $interval->format($format);
    }
}
