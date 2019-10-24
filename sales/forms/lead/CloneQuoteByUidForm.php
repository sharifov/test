<?php

namespace sales\forms\lead;

use common\models\Lead;
use common\models\Quote;
use sales\services\lead\LeadCloneQuoteService;
use yii\base\Model;

/**
 * Class CloneQuoteByUidForm
 *
 * @property $uid
 * @property $leadGid
 * @property Lead $lead
 * @property $confirm
 */
class CloneQuoteByUidForm extends Model
{

    public $uid;

    public $leadGid;

    public $confirm = 0;

    private $lead;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['leadGid', 'required'],
            ['leadGid', 'string'],
            ['leadGid', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['leadGid', 'validateLead'],


            ['uid', 'required'],
            ['uid', 'string'],
            ['uid', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['uid', 'validateItinerary'],

            ['confirm', 'string']
        ];
    }

    public function validateLead(): void
    {
        if (!$this->lead = Lead::find()->andWhere(['gid' => (string)$this->leadGid])->one()) {
            $this->addError('leadGid', 'Lead not found');
            return;
        }
    }

    public function validateItinerary(): void
    {
        if (!$quote = Quote::findOne(['uid' => $this->uid])) {
            $this->addError('uid', 'Quote not found');
            return;
        }
        if (!$this->lead) {
            $this->addError('leadGid', 'Lead not found');
            return;
        }
        if (!$leadQuote = $quote->lead) {
            $this->addError('uid', 'Not found Lead for this Quote');
            return;
        }

        try {
            LeadCloneQuoteService::guardSegments($leadQuote, $this->lead);
        } catch (\DomainException $e) {
            $this->addError('uid', $e->getMessage());
            return;
        }

        try {
            LeadCloneQuoteService::guardTypeCabin($leadQuote, $this->lead);
        } catch (\DomainException $e) {
            $this->addError('uid', $e->getMessage());
            return;
        }

        try {
            LeadCloneQuoteService::guardTypePassengers($quote, $this->lead);
        } catch (\DomainException $e) {
            $this->addError('uid', $e->getMessage());
            return;
        }

        try {
            LeadCloneQuoteService::guardCountPassengers($quote, $this->lead);
        } catch (\DomainException $e) {
            $this->addError('uid', $e->getMessage());
            return;
        }

        try {
           $this->checkCount($quote, $this->lead);
        } catch (\DomainException $e) {
            $this->addError('uid', $e->getMessage());
            return;
        }

    }

    public function checkCount(Quote $quote, Lead $lead): void
    {
        $ADT = 0;
        $CHD = 0;

        foreach ($quote->quotePrices as $price) {
            if ($price->isAdult()) {
                $ADT++;
            } elseif ($price->isChild()) {
                $CHD++;
            }
        }

        if ($ADT !== $lead->adults || $CHD !== $lead->children) {
            if ($this->confirm == $this->uid ) {
                return;
            }
            $message = 'Passenger number has been changed from';
            $ADTMeassageFrom = '';
            $ADTMeassageTo = '';
            $CHDMeassageFrom = '';
            $CHDMeassageTo = '';
            if ($ADT !== $lead->adults) {
                $ADTMeassageFrom = ' ' . $ADT . ' adult';
                $ADTMeassageTo = ' ' . $lead->adults . ' adult';
            }
            if ($CHD !== $lead->children) {
                $CHDMeassageFrom = ' ' . $CHD . ' children';
                $CHDMeassageTo = ' ' . $lead->children . ' children';
            }
            if ($ADTMeassageFrom) {
                $message .= $ADTMeassageFrom;
            }
            if ($CHDMeassageFrom) {
                if ($ADTMeassageFrom) {
                    $message .= ' and';
                }
                $message .= $CHDMeassageFrom;
            }
            $message .= ' to';
            if ($ADTMeassageTo) {
                $message .= $ADTMeassageTo;
            }
            if ($CHDMeassageTo) {
                if ($ADTMeassageTo) {
                    $message .= ' and';
                }
                $message .= $CHDMeassageTo;
            }
            $newPax = '';
            if ($lead->adults) {
                $newPax .= $lead->adults . ' adults';
            }
            if ($lead->children) {
                if ($newPax) {
                    $newPax .= ' and';
                }
                $newPax .= $lead->children . ' children';
            }
            $message .= '. This option will be cloned with new amount of passengers: ' . $newPax .  '. Are you sure?';
            $this->addError('confirm', $this->uid);
            throw new \DomainException($message);
        }
    }

}
