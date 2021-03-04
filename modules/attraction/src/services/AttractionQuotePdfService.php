<?php

namespace modules\attraction\src\services;

use common\models\Project;
use modules\attraction\models\AttractionQuote;
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
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\dispatchers\EventDispatcher;
use sales\services\pdf\GeneratorPdfService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

/**
 * Class GeneratorPdfService
 */
class AttractionQuotePdfService
{
    public const TEMPLATE_KEY = 'pdf_activity_eticket';

    public static function processingFile(AttractionQuote $attractionQuote): bool
    {
        $patchToLocalFile = self::generateAsFile($attractionQuote);

        $projectKey = self::getProjectKey($attractionQuote);
        $clientId = self::getClientId($attractionQuote);
        $leadId = self::getLeadId($attractionQuote);

        $fileStorageRepository = Yii::createObject(FileStorageRepository::class);
        $fileClientRepository = Yii::createObject(FileClientRepository::class);
        $fileOrderRepository = Yii::createObject(FileOrderRepository::class);
        $fileLeadRepository = Yii::createObject(FileLeadRepository::class);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $fileSystem = Yii::createObject(FileSystem::class);

        $title = 'ActivityConfirmationQ' . $attractionQuote->atnqProductQuote->pq_id . '.pdf';
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

        if ($fileStorage->fs_id && $orderId = ArrayHelper::getValue($attractionQuote, 'atnqProductQuote.pqOrder.or_id')) {
            $fileOrder = FileOrder::create(
                $fileStorage->fs_id,
                $orderId,
                $attractionQuote->atnq_product_quote_id,
                FileOrder::CATEGORY_CONFIRMATION
            );
            $fileOrderRepository->save($fileOrder);

            $eventDispatcher->dispatch(
                new OrderFileGeneratedEvent(
                    $orderId,
                    $fileStorage->fs_id,
                    OrderFileGeneratedEvent::TYPE_ATTRACTION_CONFIRMATION
                )
            );
        }

        if (file_exists($patchToLocalFile)) {
            FileHelper::unlink($patchToLocalFile);
        }

        return true;
    }

    public static function getContent(AttractionQuote $quote): string
    {
        $data = (new RequestPdfDataGenerator())->generate($quote);
        $content = \Yii::$app->communication->getContent(self::TEMPLATE_KEY, $data);

        if ($content['error'] !== false) {
            throw new \RuntimeException(VarDumper::dumpAsString($content['error']));
        }
        return $content['content'];
    }

    public static function generateForBrowserOutput(AttractionQuote $quote)
    {
        $content = self::getContent($quote);
        $fileName = 'activity_confirmation_Q' . $quote->atnqProductQuote->pq_id . '.pdf';

        return GeneratorPdfService::generateForBrowserOutput($content, $fileName);
    }

    public static function generateAsFile(AttractionQuote $quote): string
    {
        $content = self::getContent($quote);
        $fileName = 'activity_confirmation_Q' . $quote->atnqProductQuote->pq_id . '.pdf';

        return GeneratorPdfService::generateAsFile($content, $fileName);
    }

    public static function getProjectKey(AttractionQuote $quote): string
    {
        if ($project = ArrayHelper::getValue($quote, 'atnqProductQuote.pqProduct.prLead.project')) {
            /** @var Project $project */
            return $project->project_key;
        }
        return '';
    }

    public static function getClientId(AttractionQuote $quote): ?int
    {
        if ($client_id = ArrayHelper::getValue($quote, 'atnqProductQuote.pqProduct.prLead.client_id')) {
            return $client_id;
        }
        return null;
    }

    public static function getLeadId(AttractionQuote $quote): ?int
    {
        if ($lead_id = ArrayHelper::getValue($quote, 'atnqProductQuote.pqProduct.pr_lead_id')) {
            return $lead_id;
        }
        return null;
    }
}
