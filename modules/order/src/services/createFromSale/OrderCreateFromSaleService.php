<?php

namespace modules\order\src\services\createFromSale;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\services\CreateOrderDTO;
use yii\helpers\ArrayHelper;

/**
 * Class OrderCreateFromSaleService
 *
 * @property array $saleData
 */
class OrderCreateFromSaleService
{
    public static function create(OrderCreateFromSaleForm $form, int $saleId): Order
    {
        $dto = new CreateOrderDTO(
            null,
            $form->currency,
            [],
            OrderSourceType::SALE,
            null,
            $form->getProjectId(),
            OrderStatus::COMPLETE,
            null,
            null,
            null,
            $saleId
        );
        return (new Order())->create($dto);
    }
}
