<?php

namespace modules\hotel\src\services\hotelQuote;

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
use modules\hotel\models\HotelQuote;
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
class HotelQuotePdfService
{
    public const TEMPLATE_KEY = 'hotel_confirmation_pdf';

    /**
     * @param HotelQuote $hotelQuote
     * @return bool
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     * @throws \League\Flysystem\FilesystemException
     */
    public static function processingFile(HotelQuote $hotelQuote): bool
    {
        $patchToLocalFile = self::generateAsFile($hotelQuote);

        $projectKey = CommunicationDataService::getProjectKey($hotelQuote);
        $clientId = CommunicationDataService::getClientId($hotelQuote);
        $leadId = CommunicationDataService::getLeadId($hotelQuote);

        $fileStorageRepository = Yii::createObject(FileStorageRepository::class);
        $fileClientRepository = Yii::createObject(FileClientRepository::class);
        $fileOrderRepository = Yii::createObject(FileOrderRepository::class);
        $fileLeadRepository = Yii::createObject(FileLeadRepository::class);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $fileSystem = Yii::createObject(FileSystem::class);

        $title = 'BookingConfirmationQ' . $hotelQuote->hqProductQuote->pq_id . '.pdf';
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

        if ($fileStorage->fs_id && $orderId = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqOrder.or_id')) {
            $fileOrder = FileOrder::create(
                $fileStorage->fs_id,
                $orderId,
                $hotelQuote->hq_product_quote_id,
                FileOrder::CATEGORY_CONFIRMATION
            );
            $fileOrderRepository->save($fileOrder);

            $eventDispatcher->dispatch(
                new OrderFileGeneratedEvent(
                    $orderId,
                    $fileStorage->fs_id,
                    OrderFileGeneratedEvent::TYPE_HOTEL_CONFIRMATION
                )
            );
        }

        if (file_exists($patchToLocalFile)) {
            FileHelper::unlink($patchToLocalFile);
        }

        return true;
    }

    /**
     * @param HotelQuote $hotelQuote
     * @return string
     */
    public static function getContent(HotelQuote $hotelQuote): string
    {
        $data = CommunicationDataService::hotelConfirmationData($hotelQuote);
        $content = \Yii::$app->communication->getContent(self::TEMPLATE_KEY, $data);

        if ($content['error'] !== false) {
            throw new \RuntimeException(VarDumper::dumpAsString($content['error']));
        }
        return $content['content'];
    }

    /**
     * @param HotelQuote $hotelQuote
     * @return mixed
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateForBrowserOutput(HotelQuote $hotelQuote)
    {
        $content = self::getContent($hotelQuote);
        $fileName = 'booking_confirmation_Q' . $hotelQuote->hqProductQuote->pq_id . '.pdf';

        return GeneratorPdfService::generateForBrowserOutput($content, $fileName);
    }

    /**
     * @param HotelQuote $hotelQuote
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateAsFile(HotelQuote $hotelQuote): string
    {
        $content = self::getContent($hotelQuote);
        $fileName = 'booking_confirmation_Q' . $hotelQuote->hqProductQuote->pq_id . '.pdf';

        return GeneratorPdfService::generateAsFile($content, $fileName);
    }

    /**
     * @param HotelQuote $hotelQuote
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateAsString(HotelQuote $hotelQuote): string
    {
        $content = self::getContent($hotelQuote);

        return GeneratorPdfService::generateAsString($content);
    }
}
