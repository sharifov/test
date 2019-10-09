<?php

namespace sales\forms\leadflow;

use common\models\Lead;
use yii\base\Model;

/**
 * Class FollowUpReasonForm
 *
 * @property int $leadId
 * @property int $leadGid
 * @property string $reason
 * @property string $other
 * @property string $description
 */
class FollowUpReasonForm extends Model
{

    public const REASON_PROPER_FOLLOW_UP_DONE = 'Proper Follow Up Done';
    public const REASON_DIDNT_GET_IN_TOUCH = 'Didnt get in touch';
    public const REASON_OTHER = 'Other';

    public const REASON_LIST = [
        self::REASON_PROPER_FOLLOW_UP_DONE => self::REASON_PROPER_FOLLOW_UP_DONE,
        self::REASON_DIDNT_GET_IN_TOUCH => 'Didn\'t get in touch',
        self::REASON_OTHER => self::REASON_OTHER,
    ];

    public $leadId;
    public $leadGid;
    public $reason;
    public $other;
    public $description;

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
            ['other', 'string']
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
            return true;
        }
        return false;
    }

}
