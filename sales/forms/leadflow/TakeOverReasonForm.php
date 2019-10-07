<?php

namespace sales\forms\leadflow;

use common\models\Lead;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Class TakeOverReasonForm
 *
 * @property int $leadId
 * @property int $leadGid
 * @property int $originId
 * @property string $reason
 * @property string $other
 * @property string $description
 * @property array $origin
 */
class TakeOverReasonForm extends Model
{

    public const REASON_CLIENT_ASKED_FOR_ASSISTANCE = 'Client asked for assistance';
    public const REASON_DUPLICATE = 'Duplicate';
    public const REASON_I_AM_ORIGINAL_AGENT = 'I am original agent';
    public const REASON_OTHER = 'Other';

    public const REASON_LIST = [
        self::REASON_CLIENT_ASKED_FOR_ASSISTANCE => self::REASON_CLIENT_ASKED_FOR_ASSISTANCE,
        self::REASON_DUPLICATE => self::REASON_DUPLICATE,
        self::REASON_I_AM_ORIGINAL_AGENT => self::REASON_I_AM_ORIGINAL_AGENT,
        self::REASON_OTHER => self::REASON_OTHER,
    ];

    public $leadId;
    public $leadGid;
    public $originId;
    public $reason;
    public $other;
    public $description;

    private $origin;

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

            ['originId', 'required', 'when' => function () {
                return $this->reason === self::REASON_DUPLICATE;
            }],
            ['originId', 'integer'],
            ['originId', 'originExist'],

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

    public function originExist(): void
    {
        $this->origin = Lead::find()->select(['id', 'gid', 'status'])->active()
            ->andWhere(['id' => $this->originId])
            ->andWhere(['<>', 'id', $this->leadId])
            ->asArray()->one();
        if (!$this->origin) {
            $this->addError('originId', 'Lead with Id: ' . $this->originId . ' not found');
        }
    }

    /**
     * @return bool
     */
    public function isOtherReason(): bool
    {
        return $this->reason === self::REASON_OTHER;
    }

    /**
     * @return bool
     */
    public function isDuplicateReason(): bool
    {
        return $this->reason === self::REASON_DUPLICATE;
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
            } elseif ($this->isDuplicateReason()) {
                if (isset($this->origin['gid'])) {
                    $this->description = self::REASON_LIST[$this->reason] . '. Origin: ' . Html::a($this->originId, [
                            'lead/view','gid' => $this->origin['gid']], ['data-pjax' => 0]);
                } else {
                    $this->description = self::REASON_LIST[$this->reason] . ': undefined GID';
                }
            } else {
                $this->description = self::REASON_LIST[$this->reason];
            }
            return true;
        }
        return false;
    }

}
