<?php

namespace modules\objectTask\src\commands;

use common\components\SearchService;
use common\models\ClientEmail;
use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use common\models\QuoteCommunication;
use frontend\helpers\JsonHelper;
use frontend\helpers\QuoteHelper;
use modules\objectTask\src\entities\ObjectTask;
use src\dto\email\EmailDTO;
use src\dto\searchService\SearchServiceQuoteDTO;
use src\helpers\app\AppHelper;
use src\quoteCommunication\Repo;
use src\repositories\quote\QuoteRepository;
use src\services\email\EmailMainService;
use src\services\metrics\MetricsService;
use src\services\quote\addQuote\AddQuoteService;
use src\services\quote\quotePriceService\ClientQuotePriceService;
use Yii;
use yii\helpers\Json;

class SendEmailWithQuotes extends BaseCommand
{
    public const COMMAND = 'sendEmailWithQuotes';

    private AddQuoteService $addQuoteService;
    private QuoteRepository $quoteRepository;
    private EmailMainService $emailMainService;
    private ?Employee $agent = null;
    private ?Lead $lead = null;

    public function __construct(
        ObjectTask $objectTask,
        AddQuoteService $addQuoteService,
        QuoteRepository $quoteRepository,
        EmailMainService $emailMainService,
        array $config
    ) {
        $this->addQuoteService = $addQuoteService;
        $this->quoteRepository = $quoteRepository;
        $this->emailMainService = $emailMainService;

        parent::__construct($objectTask, $config);
    }

    public static function getConfigTemplate(): array
    {
        return [
            'markup' => [
                'amount' => 0,
                'percent' => 0,
                'defaultValue' => 100,
            ],
            'quotes' => 1,
            'templateKey' => 'templateKey',
        ];
    }

    public function process(): bool
    {
        $agent = $this->getVirtualAgent();
        $lead = $this->getLead();
        $quoteAmount = $this->getAmountQuotesForSend();

        if ($quoteAmount <= 0) {
            throw new \Exception("The number of quotas is less than or equal to zero");
        }

        $newQuotes = $this->getUniqueQuotesForLeadFromApi($quoteAmount);

        if (empty($newQuotes)) {
            throw new \Exception("Not found quotes for lead {$lead->id}");
        }

        $savedQuotes = $this->saveQuotesToLeadWithNewExtraMarkup(
            $newQuotes,
            $agent,
            $this->getLastExtraMarkupDataIfExists()
        );

        if (empty($savedQuotes)) {
            throw new \Exception("Failed save quotes with updated extra markup");
        }

        $this->sendToEmail($savedQuotes);

        return true;
    }

    protected function getVirtualAgent(): ?Employee
    {
        if ($this->agent === null) {
            $setting = Yii::$app->params['settings']['virtual_agent_list'];

            if (isset($setting) && !empty($setting)) {
                if ($this->objectTask->lead->project !== null) {
                    $projectKey = $this->objectTask->lead->project->project_key;

                    if (isset($setting[$projectKey])) {
                        $username = (string) $setting[$projectKey];

                        $this->agent = Employee::find()
                            ->where([
                                'username' => $username,
                            ])
                            ->limit(1)
                            ->one();

                        if ($this->agent === null) {
                            throw new \Exception("Not found virtual agent for project {$projectKey}");
                        }
                    }
                }
            }
        }

        return $this->agent;
    }

    protected function getLead(): Lead
    {
        if ($this->lead === null) {
            $this->lead = $this->objectTask->lead;

            if ($this->lead === null) {
                throw new \Exception("Not found lead. Object task id {$this->objectTask->ot_object_id}");
            }
        }

        return $this->lead;
    }

    protected function getLastExtraMarkupDataIfExists(): array
    {
        $lastQuote = $this->findLastQuote();
        $extraMarkup = [];

        if ($lastQuote !== null) {
            if (!empty($lastQuote->quotePrices)) {
                foreach ($lastQuote->quotePrices as $quotePrice) {
                    $extraMarkup[$quotePrice->passenger_type] = $quotePrice->extra_mark_up;
                }
            }
        }

        return $extraMarkup;
    }

    protected function saveQuotesToLeadWithNewExtraMarkup(array $quotes, Employee $agent, array $lastExtraMarkupList = []): array
    {
        $quoteList = [];
        $lead = $this->getLead();

        foreach ($quotes as $newQuote) {
            $uid = $this->addQuoteService->createByData($newQuote, $lead, null);
            $quote = Quote::find()
                ->where([
                    'uid' => $uid
                ])
                ->limit(1)
                ->one();

            $prices = $quote->quotePrices;

            if (empty($prices)) {
                throw new \Exception("Not found prices for quote {$quote->id}");
            }

            $clientQuotePriceService = new ClientQuotePriceService($quote);
            $priceData = $clientQuotePriceService->getClientPricesData();
            $sellingOld = $priceData['total']['selling'];

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                foreach ($prices as $price) {
                    $markup = $lastExtraMarkupList[$price->passenger_type] ?? 0;

                    if ($markup <= 0) {
                        $markup = $this->getMarkupDefaultValue();
                    }

                    $markup = $markup + $this->getMarkupAmount();

                    if ($this->getMarkupPercent() !== 0) {
                        $markup += $markup / 100 * $this->getMarkupPercent();
                    }

                    if ($markup > 0) {
                        $price->extra_mark_up = (1 / $quote->q_client_currency_rate) * $markup;
                        $price->qp_client_extra_mark_up = $markup;
                        $price->update();
                    }
                }

                $quote->changeExtraMarkUp($agent->id, $sellingOld);
                $this->quoteRepository->save($quote);
                $transaction->commit();
                $quoteList[] = $quote;
            } catch (\RuntimeException | \DomainException $e) {
                $transaction->rollBack();
                Yii::warning(
                    AppHelper::throwableFormatter($e),
                    'SendEmailWithQuotes::saveQuotesToLeadWithNewExtraMarkup:exception'
                );
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error(
                    AppHelper::throwableLog($e),
                    'SendEmailWithQuotes:saveQuotesToLeadWithNewExtraMarkup:Throwable'
                );
            }
        }

        return $quoteList;
    }

    /**
     * @param Quote[] $quotes
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\httpclient\Exception
     */
    private function sendToEmail(array $quotes): bool
    {
        $lead = $this->getLead();
        $agent = $this->getVirtualAgent();
        $project = $lead->project;
        $uid = QuoteCommunication::generateUid();
        $projectContactInfo = [];

        if ($project && $project->contact_info) {
            $projectContactInfo = Json::decode($project->contact_info);
        }

        $clientEmail = ClientEmail::getGeneralEmail(
            $lead->client_id
        );

        if ($clientEmail === null) {
            throw new \Exception("Not found email for lead {$lead->id}");
        }

        $dataForPreview = $lead->getEmailData2($quotes, $projectContactInfo, 'en-US', [], $agent);
        $dataForPreview['quotes'] = array_map(function ($quoteArray) use ($uid) {
            $quoteArray['qc'] = $uid;
            return $quoteArray;
        }, $dataForPreview['quotes'] ?? []);

        $preview = Yii::$app->comms->mailPreview(
            $lead->project_id,
            $this->getTemplateKey(),
            $agent->email,
            $clientEmail,
            $dataForPreview
        );

        if (isset($preview['data']) && !empty($preview['data'])) {
            $preview = $preview['data'];
            $mailDTO = EmailDTO::fromArray([
                'leadId' => $lead->id,
                'projectId' => $preview['project_id'] ?? null,
                'emailFrom' => $preview['email_from'] ?? null,
                'emailFromName' => $agent->full_name,
                'emailTo' => $preview['email_to'] ?? null,
                'emailSubject' => $preview['email_subject'] ?? null,
                'bodyHtml' => $preview['email_body_html'] ?? null,
                'languageId' => $preview['language_id'] ?? null,
                'templateKey' => $this->getTemplateKey(),
            ]);

            $mail = $this->emailMainService->createFromDTO($mailDTO);
            $this->emailMainService->sendMail($mail);

            foreach ($quotes as $quote) {
                Repo::createForEmail($mail->e_id, $quote->id, $uid);
                $quote->setStatusSend();
                if (!$this->quoteRepository->save($quote)) {
                    Yii::error($quote->errors, 'SendEmailWithQuotes::sendToEmail:exception:Quote:save');
                }
            }
        }

        return true;
    }

    protected function getUniqueQuotesForLeadFromApi(int $amount): array
    {
        $quotes = [];
        $availableQuotes = $this->getQuotesFromApi();

        if (isset($availableQuotes['count'], $availableQuotes['results']) && $availableQuotes['count'] > 0) {
            foreach ($availableQuotes['results'] as $quote) {
                $quoteExists = Quote::find()
                    ->where([
                        'AND',
                        ['lead_id' => $this->objectTask->lead->id],
                        ['<>', 'status', Quote::STATUS_DECLINED],
                        ['LIKE', 'origin_search_data', $quote['key']]
                    ])
                    ->limit(1)
                    ->exists();

                if ($quoteExists === false) {
                    $quotes[] = $quote;

                    if (count($quotes) >= $amount) {
                        break;
                    }
                }
            }
        }

        return $quotes;
    }

    protected function getQuotesFromApi(): array
    {
        $lead = $this->getLead();
        $keyCache = sprintf('command-quotes-search-%d-%s-%s', $lead->id, '', $lead->generateLeadKey());
        $quotes = \Yii::$app->cacheFile->get($keyCache);
        $dto = new SearchServiceQuoteDTO($lead);

        if ($quotes === false) {
            $timeStart = microtime(true);
            $metricsService = \Yii::$container->get(MetricsService::class);

            $quotes = SearchService::getOnlineQuotes($dto);

            $metricsService->addQuoteSearchHistogram($timeStart, 'quote_search');
            $metricsService->addQuoteSearchCounter('quote_search');
            unset($metricsService);

            if ($quotes && !empty($quotes['data']['results']) && empty($quotes['error'])) {
                \Yii::$app->cacheFile->set($keyCache, $quotes = QuoteHelper::formatQuoteData($quotes['data']), 600);
            } else {
                throw new \RuntimeException(!empty($quotes['error']) ? JsonHelper::decode($quotes['error'])['Message'] : 'No search results');
            }
        }

        return $quotes;
    }

    protected function findLastQuote(array $allowedStatusList = [Quote::STATUS_SENT, Quote::STATUS_OPENED, Quote::STATUS_APPLIED]): ?Quote
    {
        $lead = $this->getLead();

        return Quote::find()
            ->where([
                'AND',
                ['lead_id' => $lead->id],
                ['IN', 'status', $allowedStatusList]
            ])
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->limit(1)
            ->one();
    }

    public function getMarkupAmount(): int
    {
        return (int) ($this->config['markup']['amount'] ?? 0);
    }

    public function getMarkupPercent(): int
    {
        return (int) ($this->config['markup']['percent'] ?? 0);
    }

    public function getMarkupDefaultValue(): int
    {
        return (int) ($this->config['markup']['defaultValue'] ?? 0);
    }

    public function getAmountQuotesForSend(): int
    {
        return (int) ($this->config['quotes'] ?? 0);
    }

    public function getTemplateKey(): string
    {
        return $this->config['templateKey'] ?? '';
    }
}
