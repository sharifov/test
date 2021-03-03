<?php

namespace modules\order\src\services;

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
use modules\order\src\entities\order\Order;
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\dispatchers\EventDispatcher;
use sales\services\pdf\GeneratorPdfService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

/**
 * Class OrderPdfService
 */
class OrderPdfService
{
    public const TEMPLATE_KEY = 'pdf_order_receipt';

    /**
     * @param Order $order
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function processingFile(Order $order): bool
    {
        $fileStorageRepository = Yii::createObject(FileStorageRepository::class);
        $fileClientRepository = Yii::createObject(FileClientRepository::class);
        $fileOrderRepository = Yii::createObject(FileOrderRepository::class);
        $fileLeadRepository = Yii::createObject(FileLeadRepository::class);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $fileSystem = Yii::createObject(FileSystem::class);

        $patchToLocalFile = self::generateAsFile($order);

        $clientId = $order->orLead->client_id;
        $projectKey = $order->orLead->project->project_key;
        $leadId = $order->or_lead_id;

        $title = 'OrderReceiptO' .  $order->or_id . '.pdf';
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

        if ($fileStorage->fs_id) {
            $fileOrder = FileOrder::create(
                $fileStorage->fs_id,
                $order->or_id,
                null,
                FileOrder::CATEGORY_RECEIPT
            );
            $fileOrderRepository->save($fileOrder);

            $eventDispatcher->dispatch(
                new OrderFileGeneratedEvent(
                    $order->or_id,
                    $fileStorage->fs_id,
                    OrderFileGeneratedEvent::TYPE_ORDER_RECEIPT
                )
            );
        }

        if (file_exists($patchToLocalFile)) {
            FileHelper::unlink($patchToLocalFile);
        }
        return true;
    }

    public static function generateAsFile(Order $order): string
    {
        $content = self::getContent($order);
        $fileName = 'order_receipt_o' . $order->or_id . '.pdf';

        return GeneratorPdfService::generateAsFile($content, $fileName);
    }

    public static function generateForBrowserOutput(Order $order)
    {
        $content = self::getContent($order);
        $fileName = 'order_receipt_o' . $order->or_id . '.pdf';

        return GeneratorPdfService::generateForBrowserOutput($content, $fileName);
    }

    public static function getContent(Order $order): string
    {
        $data['order'] = $order->serialize();
        $data['project_key'] = $order->orLead->project->project_key;
        $content = \Yii::$app->communication->getContent(self::TEMPLATE_KEY, $data);

        if ($content['error'] !== false) {
            throw new \RuntimeException(VarDumper::dumpAsString($content['error']));
        }
        return $content['content'];
    }
}
