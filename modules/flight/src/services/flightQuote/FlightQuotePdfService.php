<?php

namespace modules\flight\src\services\flightQuote;

use common\components\BackOffice;
use common\models\Project;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\entity\fileOrder\FileOrderRepository;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\FileSystem;
use modules\fileStorage\src\services\CreateByLocalFileDto;
use modules\flight\models\FlightQuote;
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\GeneratorPdfService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use sales\dispatchers\EventDispatcher;

use function Amp\Promise\timeoutWithDefault;

/**
 * Class FlightQuotePdfService
 */
class FlightQuotePdfService
{
    /**
     * @param FlightQuote $flightQuote
     * @return bool
     * @throws \League\Flysystem\FilesystemException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function processingFile(FlightQuote $flightQuote): bool
    {
        $fileStorageRepository = Yii::createObject(FileStorageRepository::class);
        $fileClientRepository = Yii::createObject(FileClientRepository::class);
        $fileOrderRepository = Yii::createObject(FileOrderRepository::class);
        $fileLeadRepository = Yii::createObject(FileLeadRepository::class);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $fileSystem = Yii::createObject(FileSystem::class);

        $content = self::generateContentFromBO($flightQuote);
        $patchToLocalFile = self::generateEticketAsFile($flightQuote, $content);

        $projectKey = self::getProjectKey($flightQuote);
        $clientId = self::getClientId($flightQuote);
        $leadId = self::getLeadId($flightQuote);

        $title = 'E_Ticket_Q' . $flightQuote->fqProductQuote->pq_id . '.pdf';
        $createDto = new CreateByLocalFileDto($patchToLocalFile, $clientId, $projectKey, $title);
        $fileStorage = FileStorage::createByLocalFile($createDto);

        $fileSystem->write($fileStorage->fs_path, file_get_contents($patchToLocalFile));

        $fileStorageRepository->save($fileStorage);

        if ($clientId && $fileStorage->fs_id) {
            $fileClient = FileClient::create($fileStorage->fs_id, $clientId);
            $fileClientRepository->save($fileClient);
        }
        if ($leadId && $fileStorage->fs_id) {
            $fileLead = FileLead::create($fileStorage->fs_id, $leadId);
            $fileLeadRepository->save($fileLead);
        }

        if ($fileStorage->fs_id && $orderId = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqOrder.or_id')) {
            $fileOrder = FileOrder::create(
                $fileStorage->fs_id,
                $orderId,
                $flightQuote->fq_product_quote_id,
                FileOrder::CATEGORY_CONFIRMATION
            );
            $fileOrderRepository->save($fileOrder);

            $eventDispatcher->dispatch(
                new OrderFileGeneratedEvent(
                    $orderId,
                    $fileStorage->fs_id,
                    OrderFileGeneratedEvent::TYPE_FLIGHT_CONFIRMATION
                )
            );
        }

        if (file_exists($patchToLocalFile)) {
            FileHelper::unlink($patchToLocalFile);
        }
        return true;
    }

    /**
     * @param FlightQuote $flightQuote
     * @param string $type
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function generateContentFromBO(FlightQuote $flightQuote, string $type = 'e-ticket')
    {
        if (!$flightQuote->fq_flight_request_uid) {
            throw new \RuntimeException('FlightRequestUid is empty');
        }

        $requestData = [
            'uid' => [$flightQuote->fq_flight_request_uid],
            'type' => $type,
        ];
        $host = Yii::$app->params['backOffice']['serverUrlV2'];
        $responseBO = BackOffice::sendRequest2('download/files', $requestData, 'POST', 120, $host);

        if ($responseBO->isOk) {
            $status = $responseBO->getStatusCode();
            $headers = $responseBO->getHeaders();

            if (
                (int)$status === 200 &&
                ($contentType = ArrayHelper::getValue($headers, 'content-type')) &&
                $contentType === 'application/pdf'
            ) {
                return $responseBO->getContent();
            }

            $responseData = $responseBO->getData();

            if ($status = (ArrayHelper::getValue($responseData, 'status') !== null)) {
                if ($status === false && $message = ArrayHelper::getValue($responseData, 'message')) {
                    throw new \RuntimeException('FlightQuotePdfService BO response error: ' . $message);
                }
            }
            \Yii::error(
                VarDumper::dumpAsString([
                'data' => $requestData,
                'responseData' => $responseData,
                ]),
                'FlightQuotePdfService:book:failResponse'
            );
            throw new \RuntimeException('FlightQuotePdfService BO response fail');
        }
        \Yii::error(
            VarDumper::dumpAsString([
            'data' => $requestData,
            'responseContent' => $responseBO->content,
            ]),
            'FlightQuotePdfService:request'
        );
        throw new \RuntimeException('FlightQuotePdfService BO request error. ' . VarDumper::dumpAsString($responseBO->content));
    }

    public static function generateEticketAsFile(FlightQuote $flightQuote, string $content)
    {
        $fileName = 'e_ticket_Q' . $flightQuote->fqProductQuote->pq_id . '.pdf';
        $patchToDir =  \Yii::getAlias('@frontend/runtime/pdf/');
        $patchToFile = $patchToDir . $fileName;

        if (!file_exists($patchToDir)) {
            FileHelper::createDirectory($patchToDir);
        }
        if (file_exists($patchToFile)) {
            FileHelper::unlink($patchToFile);
        }

        file_put_contents($patchToFile, $content);

        return $patchToFile;
    }

    private static function getProjectKey(FlightQuote $flightQuote): string
    {
        if ($project = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqProduct.prLead.project')) {
            /** @var Project $project */
            return $project->project_key;
        }
        return '';
    }

    private static function getClientId(FlightQuote $flightQuote): ?int
    {
        if ($client_id = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqProduct.prLead.client_id')) {
            return $client_id;
        }
        return null;
    }

    private static function getLeadId(FlightQuote $flightQuote): ?int
    {
        if ($lead_id = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqProduct.pr_lead_id')) {
            return $lead_id;
        }
        return null;
    }
}
