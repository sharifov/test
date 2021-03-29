<?php

namespace modules\order\src\forms\createC2b;

use common\models\Project;
use sales\forms\CompositeForm;

/**
 * Class CreateC2BForm
 * @package modules\order\src\forms\createC2b
 *
 * @property string $projectApiKey
 * @property QuotesForm[] $quotes
 */
class OrderCreateC2BForm extends CompositeForm
{
    public $quotes;

    public $projectApiKey;

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
            ['projectApiKey', 'required'],
            ['projectApiKey', 'string', 'max' => 255],

            [['projectApiKey'], 'exist', 'targetClass' => Project::class, 'targetAttribute' => 'api_key']
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
