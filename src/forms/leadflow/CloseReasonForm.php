<?php

namespace src\forms\leadflow;

use common\models\Lead;
use common\models\ProfitSplit;
use common\models\Quote;
use modules\featureFlag\FFlag;
use src\model\leadStatusReason\entity\LeadStatusReason;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;
use src\repositories\quote\QuoteRepository;
use yii\base\Model;

class CloseReasonForm extends Model
{
    public $reasonKey;
    public $leadGid;
    public $reason;
    public $originLeadId;

    private Lead $_lead;

    private const REASON_MAX_STRING_CHAR = 255;

    public function __construct(Lead $lead, $config = [])
    {
        $this->leadGid = $lead->gid;
        $this->_lead = $lead;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['reasonKey', 'leadGid'], 'required'],
            ['reasonKey', 'exist', 'skipOnEmpty' => false, 'skipOnError' => true, 'targetClass' => LeadStatusReason::class, 'targetAttribute' => ['reasonKey' => 'lsr_key']],
            [['reason'], 'string'],
            [['reason'], 'filter', 'filter' => 'trim'],
            ['reason', 'validateReason', 'skipOnEmpty' => false],
            ['originLeadId', 'validateOriginLeadId', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'reasonKey' => 'Reason',
            'reason' => 'Comment'
        ];
    }

    public function validateReason($attribute, $params, $validator)
    {
        $commentRequired = (bool)((LeadStatusReasonQuery::getLeadStatusReasonByKey($this->reasonKey))->lsr_comment_required ?? false);
        if ($commentRequired) {
            if (empty($this->reason)) {
                $this->addError('reason', 'Comment cannot be blank');
            }

            if (strlen($this->reason) > self::REASON_MAX_STRING_CHAR) {
                $this->addError('reason', 'Comment should contain at most ' . self::REASON_MAX_STRING_CHAR . ' characters');
            }
        } else {
            $this->reason = '';
        }
    }

    public function validateOriginLeadId($attribute, $params, $validator)
    {
        if ($this->reasonKey === LeadStatusReason::REASON_KEY_DUPLICATED) {
            /** @fflag FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED, Validate Closing Reason - Duplicated Enable */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED)) {
                if (empty($this->originLeadId)) {
                    $this->addError('originLeadId', 'Origin Request cannot be blank.');
                    return true;
                }

                $leadOrigin = Lead::findOne(['id' => $this->originLeadId]);
                if (!$leadOrigin) {
                    $this->addError('originLeadId', 'Origin Request not found.');
                    return true;
                }

                if ($leadOrigin->client_id !== $this->_lead->client_id) {
                    $this->addError('originLeadId', 'Duplicate and Origin Request have different clients.');
                    return true;
                }

                $monthSecond = 30 * 24 * 60 * 60;
                if ((strtotime($this->_lead->created) - $monthSecond) > time() || (strtotime($leadOrigin->created) - $monthSecond) > time()) {
                    $this->addError('originLeadId', 'Original and Duplicate requests were created 30 days later.');
                    return true;
                }

                if (!empty($leadOrigin->bo_flight_id)) {
                    $this->addError('originLeadId', 'Origin Request has Sale');
                    return true;
                }

                $quoteRepository = \Yii::createObject(QuoteRepository::class);

                $quoteCount = $quoteRepository->getAmountQuoteByLeadIdAndStatuses(
                    $this->_lead->id,
                    [Quote::STATUS_SENT, Quote::STATUS_DECLINED, Quote::STATUS_OPENED]
                );

                if ($quoteCount > 0) {
                    $this->addError('originLeadId', 'Duplicate request must not have price quotes sent to the client.');
                    return true;
                }

                $existsProfitSplit = ProfitSplit::find()->andWhere(['ps_lead_id' => $this->_lead->id])->exists();

                if ($existsProfitSplit) {
                    $this->addError('originLeadId', 'Duplicate Request has ProfitSplit');
                    return true;
                }
            }
        }
    }
}
