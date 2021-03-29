<?php

namespace modules\order\src\forms\api\create;

use common\models\Project;
use modules\offer\src\entities\offer\Offer;
use sales\forms\CompositeRecursiveForm;

/**
 * Class OrderCreateForm
 * @package modules\order\src\forms\api
 *
 * @property string $offerGid
 * @property string $projectApiKey
 * @property ProductQuotesForm[] $productQuotes
 * @property PaymentForm $payment
 * @property BillingInfoForm $billingInfo
 * @property CreditCardForm $creditCard
 * @property TipsForm $tips
 * @property PaxesForm[] $paxes
 */
class OrderCreateForm extends CompositeRecursiveForm
{
    public string $offerGid = '';

    public string $projectApiKey = '';

    public function __construct(int $countProductQuotes, int $countPaxes, $config = [])
    {
        $productQuotesForm = [];
        for ($i = 1; $i <= $countProductQuotes; $i++) {
            $productQuotesForm[] = new ProductQuotesForm();
        }
        $this->productQuotes = $productQuotesForm;
        $this->payment = new PaymentForm();
        $this->billingInfo = new BillingInfoForm();
        $this->creditCard = new CreditCardForm();
        $this->tips = new TipsForm();
        $paxesForm = [];
        for ($i = 1; $i <= $countPaxes; $i++) {
            $paxesForm[] = new PaxesForm();
        }
        $this->paxes = $paxesForm;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['offerGid', 'projectApiKey'], 'required'],
            [['offerGid', 'projectApiKey'], 'string'],
            ['offerGid', 'exist', 'targetClass' => Offer::class, 'targetAttribute' => 'of_gid'],
            ['projectApiKey', 'exist', 'targetClass' => Project::class, 'targetAttribute' => 'api_key']
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (empty($this->productQuotes)) {
            $this->addError('productQuotes', 'Product quotes not provided');
            return false;
        }
        return parent::validate($attributeNames, $clearErrors);
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
        return ['productQuotes', 'payment', 'billingInfo', 'creditCard', 'tips', 'paxes'];
    }
}
