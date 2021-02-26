<?php

namespace modules\order\src\forms\api;

use modules\offer\src\entities\offer\Offer;
use sales\forms\CompositeForm;

/**
 * Class OrderCreateForm
 * @package modules\order\src\forms\api
 *
 * @property string $offerGid
 * @property ProductQuotesForm[] $productQuotes
 */
class OrderCreateForm extends CompositeForm
{
    public string $offerGid = '';

    public function __construct(int $countProductQuotes, $config = [])
    {
        $productQuotesForm = [];
        for ($i = 1; $i <= $countProductQuotes; $i++) {
            $productQuotesForm[] = new ProductQuotesForm();
        }
        $this->productQuotes = $productQuotesForm;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['offerGid'], 'required'],
            [['offerGid'], 'string'],
            ['offerGid', 'exist', 'targetClass' => Offer::class, 'targetAttribute' => 'of_gid']
        ];
    }

    public function formName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['productQuotes'];
    }
}
