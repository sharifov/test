<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use common\components\SearchService;
use common\models\Project;
use frontend\helpers\QuoteHelper;
use sales\dto\searchService\SearchServiceQuoteDTO;
use sales\helpers\app\AppHelper;
use sales\model\clientChatDataRequest\form\FlightSearchDataRequestForm;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class ChatDataRequestSearchFlightQuotes
 * @package common\components\jobs\clientChat
 *
 * @property-read FlightSearchDataRequestForm $form
 * @property-read string $cacheKey
 * @property-read string $chatRid
 * @property-read int|null $projectId
 */
class ChatDataRequestSearchFlightQuotesJob extends BaseJob implements JobInterface
{
    private FlightSearchDataRequestForm $form;

    private string $chatRid;

    private ?int $projectId;

    public function __construct(FlightSearchDataRequestForm $form, string $chatRid, ?int $projectId, ?float $timeStart = null, $config = [])
    {
        $this->form = $form;
        $this->chatRid = $chatRid;
        $this->projectId = $projectId;
        parent::__construct($timeStart, $config);
    }

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
            \Yii::info(VarDumper::dumpAsString($dto), 'info\ChatDataRequestSearchFlightQuotesJob::searchQuotes');
            $quotes = SearchService::getOnlineQuotes($dto);
            \Yii::info(VarDumper::dumpAsString($quotes), 'info\ChatDataRequestSearchFlightQuotesJob::searchResult');
            if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                \Yii::$app->cacheFile->set($this->cacheKey, $quotes = QuoteHelper::formatQuoteData($quotes['data']), 600);
                \Yii::info(VarDumper::dumpAsString($quotes), 'info\ChatDataRequestSearchFlightQuotesJob::quotesResult');
            }
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'ChatDataRequestSearchFlightQuotesJob::Throwable');
        }
    }

    private function getCacheKey(): string
    {
        return 'quote-search-' . $this->chatRid;
    }
}
