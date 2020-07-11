<?php

namespace sales\model\clientChat\useCase\sendOffer;

use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;

/**
 * Class GenerateImagesForm
 *
 * @property int|null $cchId
 * @property array $quotes
 * @property ClientChat $chat
 */
class GenerateImagesForm extends Model
{
    public $cchId;
    public $quotes;

    public $chat;

    public function rules(): array
    {
        return [
            ['cchId', 'required'],
            ['cchId', 'integer'],
            ['cchId', 'validateChat', 'skipOnError' => true],

            ['quotes', 'required'],
            ['quotes', \common\components\validators\IsArrayValidator::class, 'skipOnError' => true],
            ['quotes', 'each', 'rule' => ['integer'], 'skipOnError' => true],
            ['quotes', 'validateQuotes', 'skipOnError' => true],
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

    public function getAvailableQuotes(): array
    {
        if (!$this->chat) {
            return [];
        }

        if (!$quotesList = $this->chat->cchLead->getQuotesProvider([])->getModels()) {
            return [];
        }

        $quotes = [];
        foreach ($quotesList as $quote) {
            $quotes[] = $quote->id;
        }
        return $quotes;
    }

    public function validateQuotes(): void
    {
        if (!$availableQuotes = $this->getAvailableQuotes()) {
            $this->addError('cchId', 'Not found available quotes. Chad Id: ' . $this->cchId);
            return;
        }
        foreach ($this->quotes as $quote) {
            if (!in_array($quote, $availableQuotes, false)) {
                $this->addError('quotes', 'Quote Id ' . $quote . ' not in Available quotes');
                return;
            }
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
            'quotes' => 'Quotes'
        ];
    }
}
