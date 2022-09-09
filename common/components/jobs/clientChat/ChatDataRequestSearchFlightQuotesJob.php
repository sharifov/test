<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use common\components\SearchService;
use common\models\Project;
use frontend\helpers\QuoteHelper;
use src\dto\searchService\SearchServiceQuoteDTO;
use src\helpers\app\AppHelper;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatDataRequest\form\FlightSearchDataRequestForm;
use src\model\clientChatLead\entity\ClientChatLead;
use Yii;
use yii\queue\JobInterface;

/**
 * Class ChatDataRequestSearchFlightQuotes
 * @package common\components\jobs\clientChat
 *
 * @property-read FlightSearchDataRequestForm $form
 * @property-read string $cacheKey
 * @property-read int $chatId
 * @property-read int|null $projectId
 */
class ChatDataRequestSearchFlightQuotesJob extends BaseJob implements JobInterface
{
    private FlightSearchDataRequestForm $form;

    private int $chatId;

    private ?int $projectId;

    public function __construct(FlightSearchDataRequestForm $form, int $chatId, ?int $projectId, ?float $timeStart = null, $config = [])
    {
        $this->form = $form;
        $this->chatId = $chatId;
        $this->projectId = $projectId;
        parent::__construct($timeStart, $config);
    }

    public function execute($queue)
    {
        $chat = ClientChat::findOne($this->chatId);
        if (!$chat) {
            Yii::warning('Chat not found by id: ' . $this->chatId, 'ChatDataRequestSearchFlightQuotesJob::ChatNotFound');
            return;
        }

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

        $leadIds = ClientChatLead::find()->select(['ccl_lead_id'])->where(['ccl_chat_id' => $chat->cch_id])->column();
        foreach ($leadIds as $leadId) {
            $quotes = Yii::$app->cacheFile->get($this->getCacheKey($chat->cch_rid, (int)$leadId));
            if ($quotes === false || (is_array($quotes) && !empty($quotes['results']))) {
                return;
            }
        }

        try {
            $quotes = SearchService::getOnlineQuotes($dto);
            if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                Yii::$app->cacheFile->set($this->getCacheKey($chat->cch_rid, null), $quotes = QuoteHelper::formatQuoteData($quotes['data'], $dto->cid), 600);
            }
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'ChatDataRequestSearchFlightQuotesJob::Throwable');
        }
    }

    private function getCacheKey(string $chatRid, ?int $leadId): string
    {
        $key = 'quote-search-' . $chatRid;
        if ($leadId) {
            $key .= '-lead-' . $leadId;
        }
        return $key;
    }
}
