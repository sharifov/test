<?php

namespace sales\forms\leadflow;

use common\models\Lead;
use yii\base\Model;

/**
 * Class SnoozeReasonForm
 *
 * @property int $leadId
 * @property int $leadGid
 * @property string $reason
 * @property string $other
 * @property string $description
 * @property string $snoozeFor
 */
class SnoozeReasonForm extends Model
{

    public const REASON_TRAVELLING_DATES_MORE_12_MONTHS = 'Travelling dates > 12 months';
    public const REASON_NOT_READY_TO_BUY_NOW = 'Not ready to buy now';
    public const REASON_OTHER = 'Other';

    public const REASON_LIST = [
        self::REASON_TRAVELLING_DATES_MORE_12_MONTHS => 'Travelling dates > 12 months',
        self::REASON_NOT_READY_TO_BUY_NOW => self::REASON_NOT_READY_TO_BUY_NOW,
        self::REASON_OTHER => self::REASON_OTHER,
    ];

    public $leadId;
    public $leadGid;
    public $reason;
    public $other;
    public $description;
    public $snoozeFor;

    /**
     * @param Lead $lead
     * @param array $config
     */
    public function __construct(Lead $lead, $config = [])
    {
        parent::__construct($config);
        $this->leadId = $lead->id;
        $this->leadGid = $lead->gid;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['leadId', 'required'],
            ['leadId', 'integer'],
            ['leadId', 'actualLeadValidate'],

            ['reason', 'required'],
            ['reason', 'in', 'range' => array_keys(self::REASON_LIST)],

            ['other', 'required', 'when' => function () {
                return $this->reason === self::REASON_OTHER;
            }],
            ['other', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['other', 'string'],

            ['snoozeFor', 'string'],
            ['snoozeFor', function () {
                if (date('Y-m-d H:i', strtotime($this->snoozeFor)) != $this->snoozeFor) {
                    $this->addError('snoozeFor', 'Invalid format. Valid: Y-m-d H:m');
                    return;
                }
                $userTime = \Yii::$app->formatter->asDatetime(time(), 'php:Y-m-d H:i');
                if (strtotime($this->snoozeFor) <= strtotime($userTime)) {
                    $now = \Yii::$app->formatter->asDatetime($userTime, 'php:Y-m-d H:i');
                    $this->addError('snoozeFor', 'Must more then: ' . $now);
                }
            }]
        ];
    }

    public function actualLeadValidate(): void
    {
        if ($lead = Lead::find()->select(['id', 'gid'])->andWhere(['id' => $this->leadId])->asArray()->one()) {
            if ($lead['gid'] !== $this->leadGid) {
                $this->addError('leadId', 'Different leadGid');
            }
            return;
        }
        $this->addError('leadId', 'Not found Lead: ' . $this->leadId);
    }

    /**
     * @return bool
     */
    public function isOtherReason(): bool
    {
        return $this->reason === self::REASON_OTHER;
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (parent::validate($attributeNames, $clearErrors)) {
            if ($this->isOtherReason()) {
                $this->description = $this->other;
            } else {
                $this->description = self::REASON_LIST[$this->reason];
            }
            $this->snoozeFor = self::convertUserTimeToUtcTime($this->snoozeFor, \Yii::$app->formatter->timeZone);
            return true;
        }
        return false;
    }

    /**
     * @param string $date
     * @param string $userTimeZone
     * @return string
     */
    private static function convertUserTimeToUtcTime(string $date, string $userTimeZone): string
    {
        try {
            $snooze  = new \DateTime($date, new \DateTimeZone($userTimeZone));
            $snooze->setTimezone(new \DateTimeZone('UTC'));
            return $snooze->format('Y-m-d H:i');
        } catch (\Throwable $e) {
            return date('Y-m-d H:i', strtotime($date));
        }
    }

}
