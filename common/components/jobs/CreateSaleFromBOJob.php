<?php

namespace common\components\jobs;

use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\services\cases\CasesSaleService;
use yii\base\BaseObject;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 *
 * @property float|int $ttr
 * @property int $case_id
 * @property string|null $order_uid
 * @property string|null $email
 * @property string|null $phone
 * @property CasesSaleService $casesSaleService
 * @property OrderCreateFromSaleService $orderCreateFromSaleService
 * @property OrderRepository $orderRepository
 * @property FlightFromSaleService $flightFromSaleService
 */
class CreateSaleFromBOJob extends BaseJob implements JobInterface
{
    public $case_id;
    public $order_uid;
    public $email;
    public $phone;

    private $casesSaleService;
    private $orderCreateFromSaleService;
    private $orderRepository;
    private $flightFromSaleService;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        try {
            if ($this->checkParams()) {
                $this->casesSaleService = Yii::createObject(CasesSaleService::class);
                $this->orderCreateFromSaleService = Yii::createObject(OrderCreateFromSaleService::class);
                $this->orderRepository = Yii::createObject(OrderRepository::class);
                $this->flightFromSaleService = Yii::createObject(FlightFromSaleService::class);

                $saleData = $this->casesSaleService->getSaleFromBo($this->order_uid, $this->email, $this->phone);
                if (count($saleData) && isset($saleData['saleId'])) {
                    $keyCasesSale = $this->case_id . '-' . $saleData['saleId'];
                    $existCasesSale = Yii::$app->cache->get($keyCasesSale);

                    if ($existCasesSale === false) {
                        Yii::$app->cache->set($keyCasesSale, $keyCasesSale, 60);
                        $caseSale = $this->casesSaleService->createSale($this->case_id, $saleData);

                        if ($caseSale && SettingHelper::isEnableOrderFromSale()) {
                            $transaction = new Transaction(['db' => Yii::$app->db]);
                            try {
                                if (!$order = Order::findOne(['or_sale_id' => $caseSale->css_sale_id])) {
                                    $orderCreateFromSaleForm = new OrderCreateFromSaleForm();
                                    if (!$orderCreateFromSaleForm->load($saleData)) {
                                        throw new \RuntimeException('OrderCreateFromSaleForm not loaded');
                                    }
                                    if (!$orderCreateFromSaleForm->validate()) {
                                        throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($orderCreateFromSaleForm));
                                    }
                                    $order = $this->orderCreateFromSaleService->orderCreate($orderCreateFromSaleForm);

                                    $transaction->begin();
                                    $orderId = $this->orderRepository->save($order);

                                    $this->orderCreateFromSaleService->caseOrderRelation($orderId, $caseSale->css_cs_id);
                                    $this->orderCreateFromSaleService->orderContactCreate($order, OrderContactForm::fillForm($saleData));

                                    $currency = $orderCreateFromSaleForm->currency;
                                    $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $saleData);

                                    if ($authList = ArrayHelper::getValue($saleData, 'authList')) {
                                        $this->orderCreateFromSaleService->paymentCreate($authList, $orderId, $currency);
                                    }
                                    $transaction->commit();
                                } else {
                                    $this->orderCreateFromSaleService->caseOrderRelation($order->getId(), $caseSale->css_cs_id);
                                }
                            } catch (\Throwable $throwable) {
                                $transaction->rollBack();
                                $message['throwable'] = AppHelper::throwableLog($throwable, true);
                                $message['saleData'] = $saleData;
                                Yii::error($message, 'CreateSaleFromBOJob:createOrderStructureFromSale:Throwable');
                            }
                        }
                    }
                }
            } else {
                throw new \RuntimeException('Error. Params csId and (order_uid||email||phone) is required');
            }
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'CreateSaleFromBOJob:execute:catch');
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function checkParams(): bool
    {
        return ($this->case_id && ($this->order_uid || $this->email || $this->phone));
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}
