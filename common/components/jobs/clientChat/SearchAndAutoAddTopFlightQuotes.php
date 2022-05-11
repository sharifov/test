<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use common\components\SearchService;
use common\models\Lead;
use common\models\Project;
use frontend\helpers\QuoteHelper;
use src\dto\searchService\SearchServiceQuoteDTO;
use src\helpers\app\AppHelper;
use src\model\clientChatDataRequest\form\FlightSearchDataRequestForm;
use src\services\quote\addQuote\AddQuoteService;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class SearchAndAutoAddTopFlightQuotes
 * @package common\components\jobs\clientChat
 *
 * @property-read FlightSearchDataRequestForm $form
 * @property-read string $cacheKey
 * @property-read string $chatRid
 * @property-read int|null $projectId
 * @property-read int $countAddQuotes
 * @property-read int $leadId
 */
class SearchAndAutoAddTopFlightQuotes extends BaseJob implements JobInterface
{
    private FlightSearchDataRequestForm $form;

    private string $chatRid;

    private ?int $projectId;

    private int $leadId;

    private int $countAddQuotes;

    public function __construct(FlightSearchDataRequestForm $form, string $chatRid, ?int $projectId, int $leadId, int $countAddQuotes, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->form = $form;
        $this->chatRid = $chatRid;
        $this->projectId = $projectId;
        $this->leadId = $leadId;
        $this->countAddQuotes = $countAddQuotes;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $dto = new SearchServiceQuoteDTO(null);
        $dto->cabin = $this->form->getCabinCode();
        $dto->adt = $this->form->adults;
        $dto->chd = $this->form->children;
        $dto->inf = $this->form->infants;
        $dto->fl[] = [
            'o' => $this->form->originIata,
            'd' => $this->form->destinationIata,
            'dt' => $this->form->departureDate
        ];

        if ($this->form->isRoundTrip()) {
            $dto->fl[] = [
                'o' => $this->form->destinationIata,
                'd' => $this->form->originIata,
                'dt' => $this->form->returnDate
            ];
        }

        if ($this->projectId && ($project = Project::findOne($this->projectId)) && $project->airSearchCid) {
            $dto->cid = $project->airSearchCid ?? $dto->cid;
        }

        try {
            $lead = Lead::findOne($this->leadId);

            if (!$lead) {
                throw new \RuntimeException('Lead not found');
            }

            $quotes = SearchService::getOnlineQuotes($dto);
            if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                Yii::$app->cacheFile->set($this->getCacheKey(), $quotes = QuoteHelper::formatQuoteData($quotes['data']), 600);

                $dataProvider = new ArrayDataProvider([
                    'allModels' => $quotes['results'] ?? [],
                    'pagination' => [
                        'pageSize' => $this->countAddQuotes,
                    ],
                    'sort' => [
                        'attributes' => ['price', 'duration'],
                        'defaultOrder' => ['price' => SORT_ASC],
                    ],
                ]);

                $addQuoteService = Yii::createObject(AddQuoteService::class);
                $addQuoteService->autoSelectQuotes($dataProvider->getModels(), $lead, null, false, true);
            }
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'SearchAndAutoAddTopFlightQuotes::Throwable');
        }
    }

    private function getCacheKey(): string
    {
        return 'quote-search-' . $this->chatRid . '-lead-' . $this->leadId;
    }
}
