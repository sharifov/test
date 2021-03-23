<?php

namespace modules\order\src\forms\api\create;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productOption\ProductOption;
use sales\forms\CompositeRecursiveForm;

/**
 * Class ProductQuoteOptionsForm
 * @package modules\order\src\forms\api\create
 *
 * @property FlightQuoteOptionForm[] $data
 */
class ProductQuoteOptionsForm extends CompositeRecursiveForm
{
    public $productOptionKey;

    public $name;

    public $description;

    public $price;

    public $json_data;

    public function rules()
    {
        return [
            ['productOptionKey', 'string'],
            ['productOptionKey', 'required'],
            ['productOptionKey', 'exist', 'targetClass' => ProductOption::class, 'targetAttribute' => 'po_key', 'message' => 'Not found Product Option by key.'],
            ['name', 'string', 'max' => 50],
            ['description', 'safe'],
            ['price', 'required'],
            ['price', 'number'],
            ['price', 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
            ['json_data', CheckJsonValidator::class, 'skipOnEmpty' => true]
        ];
    }

    public function load($data, $formName = null, $forms = []): bool
    {
        $flightQuoteOptions = [];
        if (isset($data['data']) && $flightQuoteOptionsCount = count($data['data'])) {
            for ($i = 1; $i <= $flightQuoteOptionsCount; $i++) {
                $flightQuoteOptions[] = new FlightQuoteOptionForm();
            }
        }
        $this->data = $flightQuoteOptions;
        return parent::load($data, $formName, $forms);
    }

    public function formName(): string
    {
        return 'productOptions';
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['data'];
    }
}
