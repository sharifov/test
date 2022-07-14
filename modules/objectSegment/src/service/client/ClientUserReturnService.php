<?php

namespace modules\objectSegment\src\service\client;

use common\models\query\LeadFlowQuery;
use modules\objectSegment\src\contracts\ObjectSegmentAssigmentServiceInterface;
use modules\objectSegment\src\entities\ObjectSegmentListQuery;
use src\helpers\app\AppHelper;
use src\model\clientData\entity\ClientData;
use src\model\clientData\entity\ClientDataQuery;
use src\model\clientData\repository\ClientDataRepository;
use src\model\clientDataKey\entity\ClientDataKeyDictionary;
use src\model\clientDataKey\service\ClientDataKeyService;
use src\model\clientUserReturn\entity\ClientUserReturn;
use src\model\clientUserReturn\entity\ClientUserReturnQuery;
use src\model\clientUserReturn\entity\ClientUserReturnRepository;
use src\repositories\client\ClientRepository;
use src\repositories\NotFoundException;

/**
 * Class ClientUserReturnService
 * @package modules\objectSegment\src\service\client
 *
 * @property-read ClientRepository $clientRepository
 * @property-read ClientUserReturnRepository $clientUserReturnRepository
 */
class ClientUserReturnService implements ObjectSegmentAssigmentServiceInterface
{
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;
    /**
     * @var ClientUserReturnRepository
     */
    private ClientUserReturnRepository $clientUserReturnRepository;

    public function __construct(
        ClientRepository $clientRepository,
        ClientUserReturnRepository $clientUserReturnRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->clientUserReturnRepository = $clientUserReturnRepository;
    }

    public function assign(int $entityId, array $values): void
    {
        try {
            $client = $this->clientRepository->find($entityId);

            if (!$keyId = ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::CLIENT_RETURN)) {
                throw new \RuntimeException('ClientDataKey not found (' . ClientDataKeyDictionary::CLIENT_RETURN . ')');
            }
        } catch (NotFoundException $e) {
            \Yii::warning('Client Not found by id: ' . $entityId, 'ClientUserReturnService::assign');
            return;
        } catch (\RuntimeException $e) {
            \Yii::warning(AppHelper::throwableFormatter($e), 'ClientUserReturnService::assign');
            return;
        }

        ClientDataQuery::removeByClientAndKey($client->id, $keyId);
        foreach ($values as $value) {
            $objectSegmentList = ObjectSegmentListQuery::getByKey($value);

            try {
                $clientData = ClientData::create(
                    $client->id,
                    $keyId,
                    $value,
                    $objectSegmentList->osl_title ?? null
                );
                $clientDataRepository = new ClientDataRepository($clientData);
                $clientDataRepository->save();
            } catch (\RuntimeException $e) {
                \Yii::warning('ClientData creation failed due to: ' . AppHelper::throwableFormatter($e), 'ClientUserReturnService::assign');
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'ClientUserReturnService::assign::Throwable');
            }
        }

        $leadFlows = LeadFlowQuery::findSoldByClient($client->id);
        foreach ($leadFlows as $leadFlow) {
            if (!empty($leadFlow->lf_owner_id)) {
                try {
                    if (!ClientUserReturnQuery::exists($client->id, $leadFlow->lf_owner_id)) {
                        $clientReturn = ClientUserReturn::create($leadFlow->lf_owner_id, $client->id);
                        $this->clientUserReturnRepository->save($clientReturn);
                    }
                } catch (\RuntimeException $e) {
                    \Yii::warning('ClientUserReturn creation failed: ' . AppHelper::throwableFormatter($e), 'ClientUserReturnService::assign');
                } catch (\Throwable $e) {
                    \Yii::error(AppHelper::throwableLog($e, true), 'ClientUserReturnService::assign::Throwable');
                }
            }
        }
    }
}
