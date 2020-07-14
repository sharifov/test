<?php

namespace sales\model\clientChat\useCase\sendOffer;

use common\models\Quote;
use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;

/**
 * Class GenerateImagesForm
 *
 * @property int|null $cchId
 * @property array $quotesIds
 * @property array $quotes
 * @property ClientChat $chat
 */
class GenerateImagesForm extends Model
{
    public $cchId;
    public $quotesIds;
    public $quotes;

    public $chat;

    public function rules(): array
    {
        return [
            ['cchId', 'required'],
            ['cchId', 'integer'],
            ['cchId', 'validateChat', 'skipOnError' => true],

            ['quotesIds', 'required'],
            ['quotesIds', \common\components\validators\IsArrayValidator::class, 'skipOnError' => true],
            ['quotesIds', 'each', 'rule' => ['integer'], 'skipOnError' => true],
            ['quotesIds', 'validateQuotes', 'skipOnError' => true],
        ];
    }

    public function validateChat(): void
    {
        if (!$this->chat = ClientChat::findOne($this->cchId)) {
            $this->addError('cchId', 'Not found Client Chad with Id: ' . $this->cchId);
            return;
        }
        if (!$this->chat->cchLead) {
            $this->chat = null;
            $this->addError('cchId', 'Not found Lead relation. Chad Id: ' . $this->cchId);
            return;
        }
    }

    /**
     * @return Quote[]
     */
    public function getAvailableQuotes(): array
    {
        if (!$this->chat) {
            return [];
        }

        if (!$quotesList = $this->chat->cchLead->getQuotesProvider([], [Quote::STATUS_CREATED, Quote::STATUS_SEND, Quote::STATUS_OPENED])->getModels()) {
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
            $this->addError('cchId', 'Not found available quotes. Chad Id: ' . $this->cchId);
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
            'cchId' => 'Chat Id',
            'quotesIds' => 'Quotes'
        ];
    }
}
