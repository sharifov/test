<?php

namespace modules\order\src\services;

use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileOrder\FileOrderRepository;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\FileSystem;
use modules\order\src\entities\order\Order;
use sales\services\pdf\GeneratorPdfService;
use Yii;

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
        $fileSystem = Yii::createObject(FileSystem::class);

        $patchToLocalFile = self::generateAsFile($order);

        /* TODO:: public */

        return true;
    }

    public static function generateAsFile(Order $order): string
    {
        /* TODO::  */
        //$content = self::getContent($order);
        //$fileName = 'booking_confirmation_Q' . $hotelQuote->hqProductQuote->pq_id . '.pdf';

        //return GeneratorPdfService::generateAsFile($content, $fileName);
        return '';
    }
}
