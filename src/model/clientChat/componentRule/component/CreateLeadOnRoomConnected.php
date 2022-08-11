<?php

namespace src\model\clientChat\componentRule\component;

use common\components\jobs\clientChat\SearchAndAutoAddTopFlightQuotes;
use common\components\jobs\LeadObjectSegmentJob;
use common\models\Lead;
use frontend\helpers\JsonHelper;
use modules\featureFlag\FFlag;
use src\forms\lead\ItineraryEditForm;
use src\helpers\app\AppHelper;
use src\model\clientChat\componentEvent\component\ComponentDTOInterface;
use src\model\clientChat\componentRule\component\defaultConfig\CreateLeadOnRoomConnectedConfig;
use src\model\clientChatDataRequest\entity\ClientChatDataRequest;
use src\model\clientChatDataRequest\form\FlightSearchDataRequestForm;
use src\model\lead\useCases\lead\create\CreateLeadByChatDTO;
use src\model\lead\useCases\lead\create\LeadCreateByChatForm;
use src\model\lead\useCases\lead\create\LeadManageService;
use src\services\quote\addQuote\AddQuoteService;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;

/**
 * Class CreateLeadOnRoomConnected
 * @package src\model\clientChat\componentRule\component
 *
 * @property-read LeadManageService $leadManageService
 * @property-read AddQuoteService $addQuoteService
 */
class CreateLeadOnRoomConnected implements RunnableComponentInterface
{
    private LeadManageService $leadManageService;
    private AddQuoteService $addQuoteService;

    public function __construct(LeadManageService $leadManageService, AddQuoteService $addQuoteService)
    {
        $this->leadManageService = $leadManageService;
        $this->addQuoteService = $addQuoteService;
    }

    public function run(ComponentDTOInterface $dto): void
    {
        $createLeadIfAlreadyExists = $dto->getRunnableComponentConfig()['create_lead_if_already_exists'] ?? $this->getDefaultConfig()['create_lead_if_already_exists'];
        $chat = $dto->getClientChatEntity();

        if ($chat && ((($leads = $chat->leads) && $createLeadIfAlreadyExists) || (!$leads))) {
            $createLeadByChatForm = new LeadCreateByChatForm($chat);

            if ($createLeadByChatForm->validate()) {
                $lead = $this->leadManageService->createByClientChat((new CreateLeadByChatDTO($createLeadByChatForm, $chat, null))->leadNewDataPrepare());

                $addTopQuotesEnabled = (bool)($dto->getRunnableComponentConfig()['add_top_quotes']['enabled'] ?? $this->getDefaultConfig()['add_top_quotes']['enabled']);
                $addTopQuotesCount = (int)($dto->getRunnableComponentConfig()['add_top_quotes']['count'] ?? $this->getDefaultConfig()['add_top_quotes']['count']);

                if (!$addTopQuotesEnabled || !$addTopQuotesCount) {
                    return;
                }

                $quotes = Yii::$app->cacheFile->get($this->getCacheKey($chat->cch_rid));
                if ($quotes) {
                    $this->addAutoQuotes($lead, $addTopQuotesCount);
                } else {
                    $chatDataRequest = ClientChatDataRequest::find()->byChatId($chat->cch_id)->one();
                    if (!$chatDataRequest || (!$chatDataRequest->ccdr_data_json || !is_array($chatDataRequest->ccdr_data_json))) {
                        Yii::warning('Chat Data Request not exists for searching flight quotes', 'CreateLeadOnRoomConnected::ClientChatDataRequest::NotFound');
                        return;
                    }

                    $form = new FlightSearchDataRequestForm($chatDataRequest->ccdr_data_json);
                    if (!$form->validate()) {
                        Yii::warning('FlightSearchDataRequestForm validation failed: ' . VarDumper::dumpAsString($form->errors), 'CreateLeadOnRoomConnected::FlightSearchDataRequestForm::validationFailed');
                        return;
                    }

                    try {
                        $itineraryForm = new ItineraryEditForm($lead, $form->isRoundTrip() ? 2 : 1);
                        $itineraryForm->fillInByChatDataRequestForm($form);

                        if (!$itineraryForm->validate()) {
                            Yii::warning('ItineraryEditForm validation failed: ' . VarDumper::dumpAsString($itineraryForm->errors), 'CreateLeadOnRoomConnected::ItineraryEditForm::validationFailed');
                            return;
                        }

                        $leadManageService = Yii::createObject(\src\services\lead\LeadManageService::class);
                        $leadManageService->editItinerary($lead, $itineraryForm);
                        /** @fflag FFlag::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE, Object Segment module enable/disable */
                        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE)) {
                            $job = new LeadObjectSegmentJob($lead);
                            \Yii::$app->queue_job->priority(100)->push($job);
                        }
                        $job = new SearchAndAutoAddTopFlightQuotes($form, $chat->cch_rid, $chat->cch_project_id, $lead->id, $addTopQuotesCount);
                        \Yii::$app->queue_client_chat_job->priority(10)->push($job);
                    } catch (\RuntimeException | \DomainException $e) {
                        Yii::warning(AppHelper::throwableFormatter($e), 'CreateLeadOnRoomConnected::LeadEditItinerary');
                    }
                }
            }
        }
    }

    public function getDefaultConfig(): array
    {
        return CreateLeadOnRoomConnectedConfig::getConfig();
    }

    public function getDefaultConfigJson(): string
    {
        return JsonHelper::encode($this->getDefaultConfig());
    }

    private function getCacheKey(string $chatRid): string
    {
        return 'quote-search-' . $chatRid;
    }

    private function addAutoQuotes(Lead $lead, int $count): void
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => $quotes['results'] ?? [],
            'pagination' => [
                'pageSize' => $count,
            ],
            'sort' => [
                'attributes' => ['price', 'duration'],
                'defaultOrder' => ['price' => SORT_ASC],
            ],
        ]);
        $this->addQuoteService->autoSelectQuotes($dataProvider->getModels(), $lead, null, false, true);
    }
}
