<?php

namespace common\components\jobs;

use common\components\SearchService;
use common\models\Lead;
use frontend\helpers\JsonHelper;
use frontend\helpers\QuoteHelper;
use src\dto\searchService\SearchServiceQuoteDTO;
use src\helpers\app\AppHelper;
use src\services\quote\addQuote\AddQuoteService;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

class AutoAddQuoteJob extends BaseJob implements JobInterface
{
    public Lead $lead;

    public function __construct(Lead $lead, ?float $timeStart = null, array $config = [])
    {
        $this->lead = $lead;
        parent::__construct($timeStart, $config);
    }

    public function execute($queue)
    {
        try {
            $dto = new SearchServiceQuoteDTO($this->lead);
            $dto->setCid($this->lead->project->airSearchCid ?: AddQuoteService::AUTO_ADD_CID);

            $quotes = SearchService::getOnlineQuotes($dto);
            if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                $quotes = QuoteHelper::formatQuoteData($quotes['data']);

                $models = array_filter($quotes['results'] ?? [], function ($item) {
                    return $item['meta']['auto'] ?? false;
                });

                $dataProvider = new ArrayDataProvider([
                    'allModels' => $models,
                    'pagination' => false,
                    'sort' => [
                        'defaultOrder' => ['autoSort' => SORT_ASC, 'price' => SORT_ASC],
                        'attributes' => [
                            'price' => [
                                'asc' => ['price' => SORT_ASC],
                                'desc' => ['price' => SORT_DESC],
                                'default' => SORT_ASC,
                            ],
                            'autoSort' => [
                                'asc' => ['autoSort' => SORT_ASC],
                                'desc' => ['autoSort' => SORT_DESC],
                                'default' => SORT_ASC,
                            ],
                        ],
                    ],
                ]);
                $addQuoteService = \Yii::createObject(AddQuoteService::class);
                $addQuoteService->autoSelectQuotes($dataProvider->getModels(), $this->lead, null, true, true);
                return;
            }
            throw new \RuntimeException(!empty($quotes['error']) ? JsonHelper::decode($quotes['error'])['Message'] : 'Search result is empty!');
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['lead_id' => $this->lead->id]);
            Yii::warning($message, 'AutoAddQuoteJob::execute::Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), ['lead_id' => $this->lead->id]);
            Yii::warning($message, 'AutoAddQuoteJob::execute::Throwable');
        }
    }
}
