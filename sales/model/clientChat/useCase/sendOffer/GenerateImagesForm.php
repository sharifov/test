<?php

namespace sales\model\clientChat\useCase\sendOffer;

use common\models\Lead;
use common\models\Quote;
use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;

/**
 * Class GenerateImagesForm
 *
 * @property int|null $chatId
 * @property int|null $leadId
 * @property array $quotesIds
 * @property array $quotes
 * @property ClientChat $chat
 * @property Lead $lead
 */
class GenerateImagesForm extends Model
{
    public $chatId;
    public $leadId;
    public $quotesIds;
    public $quotes;

    public $chat;
    public $lead;

    public function rules(): array
    {
        return [
            ['chatId', 'required'],
            ['chatId', 'integer'],
            ['chatId', 'validateChat', 'skipOnError' => true],

            ['leadId', 'required'],
            ['leadId', 'integer'],
            ['leadId', 'validateLead', 'skipOnError' => true],

            ['quotesIds', 'required'],
            ['quotesIds', \common\components\validators\IsArrayValidator::class, 'skipOnError' => true],
            ['quotesIds', 'each', 'rule' => ['integer'], 'skipOnError' => true],
            ['quotesIds', 'validateQuotes', 'skipOnError' => true],
        ];
    }

    public function validateChat(): void
    {
        if (!$this->chat = ClientChat::findOne($this->chatId)) {
            $this->addError('chatId', 'Not found Client Chad with Id: ' . $this->chatId);
            return;
        }
    }

    public function validateLead(): void
    {
        if (!$this->chat) {
            return;
        }

        if (!$this->lead = Lead::findOne($this->leadId)) {
            $this->addError('leadId', 'Not found Lead. Client Chad with Id: ' . $this->chatId);
            return;
        }

        if (!$this->chat->isAssignedLead($this->lead->id)) {
            $this->addError('leadId', 'Lead (' . $this->lead->id . ') is not assigned to Client Chat. Client Chad with Id: ' . $this->chatId);
            return;
        }

        if (!$this->lead->isExistQuotesForSend()) {
            $this->addError('leadId', 'Lead (' . $this->lead->id . '). Not found Quote for Send. Client Chad with Id: ' . $this->chatId);
            return;
        }
    }

    /**
     * @return Quote[]
     */
    public function getAvailableQuotes(): array
    {
        if (!$this->chat || !$this->lead) {
            return [];
        }

        if (!$quotesList = $this->lead->getQuotesProvider([], [Quote::STATUS_CREATED, Quote::STATUS_SEND, Quote::STATUS_OPENED])->getModels()) {
            return [];
        }

        $quotes = [];
        foreach ($quotesList as $quote) {
            $quotes[$quote->id] = $quote;
        }
        return $quotes;
    }

    public function validateQuotes(): void
    {
        if (!$availableQuotes = $this->getAvailableQuotes()) {
            $this->addError('chatId', 'Not found available quotes. Chad Id: ' . $this->chatId);
            return;
        }
        foreach ($this->quotesIds as $quoteId) {
            if (!array_key_exists($quoteId, $availableQuotes)) {
                $this->addError('quotes', 'Quote Id ' . $quoteId . ' not in Available quotes');
                return;
            }
            $this->quotes[] = $availableQuotes[$quoteId];
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function attributeLabels(): array
    {
        return [
            'chatId' => 'Chat Id',
            'leadId' => 'Lead Id',
            'quotesIds' => 'Quotes'
        ];
    }
}
