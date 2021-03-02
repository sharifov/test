<?php

namespace sales\model\billingInfo\entity\serializer;

use common\models\BillingInfo;
use sales\entities\serializer\Serializer;
use sales\helpers\CountryHelper;
use yii\helpers\ArrayHelper;

/**
 * Class BillingInfoSerializer
 * @package sales\model\billingInfo\entity\serializer
 *
 * @property BillingInfo $model
 */
class BillingInfoSerializer extends Serializer
{

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'bi_first_name',
            'bi_last_name',
            'bi_middle_name',
            'bi_company_name',
            'bi_address_line1',
            'bi_address_line2',
            'bi_city',
            'bi_state',
            'bi_country',
            'bi_zip',
            'bi_contact_phone',
            'bi_contact_email',
            'bi_contact_name',
            'bi_payment_method_id'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $data = $this->toArray();

        $data['bi_country_name'] = ArrayHelper::map(CountryHelper::getCountries(), 'alpha2', 'name')[mb_strtolower($data['bi_country'])] ?? 'Unknown Country';
        $data['bi_payment_method_name'] = $this->model->paymentMethod->pm_name ?? 'Unknown payment name';

        return $data;
    }
}
