<?php

namespace sales\model\clientChatCase\service;

use sales\entities\cases\Cases;
use sales\helpers\ErrorsToStringHelper;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\clientChatCase\entity\ClientChatCaseRepository;

/**
 * Class ClientChatCaseManageService
 * @package sales\model\clientChatCase\service
 *
 * @property-read ClientChatCaseRepository $repository
 */
class ClientChatCaseManageService
{

    /**
     * @var ClientChatCaseRepository
     */
    private ClientChatCaseRepository $repository;

    public function __construct(ClientChatCaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function assignChatByCaseIds(array $ids, int $chatId): void
    {
        foreach ($ids as $caseId) {
            if (Cases::find()->where(['cs_id' => $caseId])->exists()) {
                $clientChatCase = ClientChatCase::create($chatId, $caseId, new \DateTimeImmutable('now'));
                if ($clientChatCase->validate()) {
                    $this->repository->save($clientChatCase);
                } else {
                    \Yii::warning(
                        ErrorsToStringHelper::extractFromModel($clientChatCase),
                        'ClientChatCaseManageService:assignChatByCaseIds:validate'
                    );
                }
            }
        }
    }
}
