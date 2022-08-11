<?php

namespace frontend\widgets\sale;

use yii\base\Widget;

class SaleWidget extends Widget
{
    public int $leadId;
    public int $saleId;

    public function run(): string
    {
        return $this->render('sale-detail', [
            'leadId' => $this->leadId,
            'saleId' => $this->saleId,
        ]);
    }
}
