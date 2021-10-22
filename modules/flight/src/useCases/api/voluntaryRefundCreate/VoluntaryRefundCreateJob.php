<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\models\CaseSale;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundQuery;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\CasesQuery;
use sales\exception\BoResponseException;
use sales\exception\ValidationException;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCreateService;
use sales\services\cases\CasesSaleService;
use webapi\src\ApiCodeException;
use yii\helpers\ArrayHelper;
use yii\queue\Queue;

/**
 * Class VoluntaryRefundCreateJob
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property int $flightRequestId
 * @property int|null $productQuoteId
 */
class VoluntaryRefundCreateJob extends \common\components\jobs\BaseJob implements \yii\queue\JobInterface
{
    public int $flightRequestId;
    public ?int $productQuoteId;

    public function __construct(int $flightRequestId, ?int $productQuoteId, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->flightRequestId = $flightRequestId;
        $this->productQuoteId = $productQuoteId;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        $voluntaryRefundService = \Yii::createObject(VoluntaryRefundService::class);

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flightRequestId)) {
                throw new \DomainException('FlightRequest not found, ID (' . $this->flightRequestId . ')');
            }

            $voluntaryRefundCreateForm = new VoluntaryRefundCreateForm();
            if (!$voluntaryRefundCreateForm->load(JsonHelper::decode($flightRequest->fr_data_json, true)) || !$voluntaryRefundCreateForm->validate()) {
                throw new \RuntimeException('Invalid FlightRequest data json');
            }

            if ($this->productQuoteId && $productQuote = ProductQuote::findOne(['pq_id' => $this->productQuoteId])) {
                $voluntaryRefundService->processProductQuote($productQuote, $voluntaryRefundCreateForm, $flightRequest->fr_project_id);
            } else {
                $voluntaryRefundService->startRefundAutoProcess($voluntaryRefundCreateForm, $flightRequest->fr_project_id, null);
            }
        } catch (VoluntaryRefundCodeException $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
        } catch (\Throwable $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::error(AppHelper::throwableLog($e, true), 'VoluntaryRefundCreateJob:RuntimeException:DomainException:');
        }
    }
}
