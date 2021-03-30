<?php

namespace modules\order\src\forms\api\createC2b;

use common\models\Project;
use sales\forms\CompositeForm;

/**
 * Class CreateC2BForm
 * @package modules\order\src\forms\createC2b
 *
 * @property QuotesForm[] $quotes
 */
class OrderCreateC2BForm extends CompositeForm
{
    public function __construct(int $cntQuotes, $config = [])
    {
        $quotesForm = [];
        for ($i = 1; $i <= $cntQuotes; $i++) {
            $quotesForm[] = new QuotesForm();
        }
        $this->quotes = $quotesForm;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
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
        return ['quotes'];
    }
}
