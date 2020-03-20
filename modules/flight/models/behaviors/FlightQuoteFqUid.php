<?php

namespace modules\flight\models\behaviors;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class ProductQuoteProfitAmount
 * @package modules\product\src\entities\productQuote\behaviors
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 */
class ProductQuoteProfitAmount extends Behavior
{
    private $recalculateProfitAmountService;

    /**
     * ProductQuoteProfitAmount constructor.
     * @param RecalculateProfitAmountService $recalculateProfitAmountService
     * @param array $config
     */
    public function __construct(RecalculateProfitAmountService $recalculateProfitAmountService, $config = [])
    {
        $this->recalculateProfitAmountService = $recalculateProfitAmountService;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            //ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            //ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'customAfterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'customAfterSave',
        ];
    }

    /**
     * @param $event
     * @throws \yii\base\InvalidConfigException
     */
    public function customAfterSave($event): void
    {
        if (array_key_exists('pq_profit_amount', $event->changedAttributes) ||
            array_key_exists('pq_status_id', $event->changedAttributes)
        ) {
            /** @var ProductQuote $this->owner */
            $this->recalculateProfitAmountService->setOffers($this->owner->opOffers)->recalculateOffers();
            $this->recalculateProfitAmountService->setOrders($this->owner->orpOrders)->recalculateOrders();
        }
    }

    public function beforeInsert(): void
    {
        $this->owner->pq_profit_amount = 0.00; // if need
    }

    public function beforeUpdate(): void
    {
        // example
    }
}   
