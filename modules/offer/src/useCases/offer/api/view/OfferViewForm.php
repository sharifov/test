<?php

namespace modules\offer\src\useCases\offer\api\view;

use sales\forms\api\VisitorForm;
use sales\forms\CompositeForm;
use common\components\validators\IsNotArrayValidator;

/**
 * Class OfferViewForm
 *
 * @property string $offerGid
 *@property VisitorForm $visitor
 */
class OfferViewForm extends CompositeForm
{
    public $offerGid;

    public function __construct($config = [])
    {
        $this->visitor = new VisitorForm();

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['offerGid', 'required'],
            ['offerGid', 'string'],
            ['offerGid', IsNotArrayValidator::class],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    protected function internalForms(): array
    {
        return ['visitor'];
    }
}
