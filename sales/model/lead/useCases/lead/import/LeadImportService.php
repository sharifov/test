<?php

namespace sales\model\lead\useCases\lead\import;

use common\models\Lead;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\lead\LeadRepository;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class LeadImportService
 *
 * @property LeadRepository $leadRepository
 * @property TransactionManager $transactionManager
 * @property ClientManageService $clientManageService
 */
class LeadImportService
{
    private $leadRepository;
    private $transactionManager;
    private $clientManageService;

    public function __construct(
        LeadRepository $leadRepository,
        TransactionManager $transactionManager,
        ClientManageService $clientManageService
    )
    {
        $this->leadRepository = $leadRepository;
        $this->transactionManager = $transactionManager;
        $this->clientManageService = $clientManageService;
    }

    /**
     * @param LeadImportForm[] $forms
     * @param int|null $creatorId
     * @return Log
     */
    public function import(array $forms, ?int $creatorId): Log
    {
        $log = new Log();

        foreach ($forms as $key => $form) {

            if ($form->validate()) {

                try {
                    /** @var Lead $lead */
                    $lead = $this->transactionManager->wrap(function () use ($form, $creatorId) {
                        $client = $this->clientManageService->getOrCreate(
                            [new PhoneCreateForm(['phone' => $form->client->phone])],
                            [new EmailCreateForm(['email' => $form->client->email])],
                            new ClientCreateForm([
                                'firstName' => $form->client->first_name,
                                'lastName' => $form->client->last_name,
                            ])
                        );
                        $this->guardDuplicate($form, $client->id);
                        $lead = Lead::createNew($form, $client, $creatorId);
                        $this->leadRepository->save($lead);

                        return $lead;
                    });
                    $log->add(Message::createValid($key, $lead->id));
                } catch (\Throwable $e) {
                    $log->add(Message::createInvalid($key, VarDumper::dumpAsString($e->getMessage())));
                }

            } else {
                $log->add(Message::createInvalid($key, VarDumper::dumpAsString($form->getErrors())));
            }
        }

        return $log;
    }

    private function guardDuplicate(LeadImportForm $form, int $clientId): void
    {
        if ($lead = Lead::find()->andWhere([
            'status' => Lead::STATUS_NEW,
            'client_id' => $clientId,
            'notes_for_experts' => $form->notes,
            'project_id' => $form->project_id,
            'source_id' => $form->source_id,
        ])->limit(1)->one()) {
            throw new \DomainException('Duplicate found. Lead Id: ' . $lead->id);
        }
    }
}
