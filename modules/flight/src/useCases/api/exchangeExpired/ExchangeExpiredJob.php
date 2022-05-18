<?php

declare(strict_types=1);

namespace modules\flight\src\useCases\api\exchangeExpired;

use common\components\jobs\BaseJob;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteData\service\ProductQuoteDataManageService;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\helpers\app\AppHelper;
use src\repositories\NotFoundException;
use Throwable;
use Yii;
use yii\queue\JobInterface;

class ExchangeExpiredJob extends BaseJob implements JobInterface
{
    private int $flightRequestId;
    private int $productQuoteId;
    private int $typeProductRelation;

    public function __construct(
        int $flightRequestId,
        int $productQuoteId,
        int $typeProductRelation,
        ?float $timeStart = null,
        $config = []
    ) {
        parent::__construct($timeStart, $config);
        $this->flightRequestId = $flightRequestId;
        $this->productQuoteId = $productQuoteId;
        $this->typeProductRelation = $typeProductRelation;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flightRequestId)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flightRequestId . ')');
            }

            if (!$originQuote = ProductQuoteQuery::getOriginProductQuoteByChangeQuote($this->productQuoteId)) {
                throw new NotFoundException('Origin Quote Not Found');
            }

            $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);
            $productQuoteDataManageService = Yii::createObject(ProductQuoteDataManageService::class);

            $productQuote = $productQuoteRepository->find($this->productQuoteId);
            $productQuote->expired();
            $productQuoteRepository->save($productQuote);

            $lastReProtectionQuote = ProductQuote::find()
                ->with('productQuoteDataRecommended')
                ->innerJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id and pqr_parent_pq_id = :parentQuoteId and pqr_type_id = :typeId', [
                    'typeId' => $this->typeProductRelation,
                    'parentQuoteId' => $originQuote->pq_id
                ])
                ->andWhere(['not in', 'pq_status_id', [ProductQuoteStatus::DECLINED, ProductQuoteStatus::EXPIRED]])
                ->orderBy(['pq_id' => SORT_DESC])
                ->one();

            if ($lastReProtectionQuote) {
                $productQuoteDataManageService->updateRecommendedChangeQuote(
                    $originQuote->pq_id,
                    $lastReProtectionQuote->pq_id
                );
            }

            $flightRequest->statusToExpired();
            $flightRequest->save(false);
        } catch (Throwable $e) {
            if (!empty($flightRequest)) {
                $flightRequest->statusToError();
                $flightRequest->save(false);
            }

            Yii::error([
                'product__gid' => $productQuote->pq_gid ?? null,
                'trace' => AppHelper::throwableLog($e, true)
            ], 'ExchangeExpiredJob::execute');
        }
    }
}
