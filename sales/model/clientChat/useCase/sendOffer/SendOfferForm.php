<?php

namespace sales\model\clientChat\useCase\sendOffer;

use modules\offer\src\entities\offer\Offer;

/**
 * Class SendOfferForm
 * @package sales\model\clientChat\useCase\sendOffer
 *
 * @property int $chatId
 * @property int $leadId
 * @property array $offersIds
 * @property Offer[] $offers
 */
class SendOfferForm extends \yii\base\Model
{
    public $chatId;

    public $leadId;

    public $offersIds;

    public function rules(): array
    {
        return [
            [['chatId', 'leadId'], 'integer'],
            [['chatId', 'leadId', 'offersIds'], 'required'],
            [['chatId', 'leadId'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['offersIds'], 'each', 'rule' => ['integer']],
            [['offersIds'], 'each', 'rule' => ['exist', 'targetClass' => Offer::class, 'targetAttribute' => 'of_id', 'skipOnEmpty' => true, 'message' => 'Offer not found by id {value}']],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getOffers()
    {
        $offers = [];
        foreach ($this->offersIds as $offerId) {
            $offers[] = Offer::findOne(['of_id' => $offerId]);
        }
        return $offers;
    }
}
