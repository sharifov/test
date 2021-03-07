<?php

namespace modules\rentCar\src\services;

use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\entity\fileOrder\FileOrderRepository;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\services\CreateByLocalFileDto;
use modules\order\src\entities\order\Order;
use modules\order\src\events\OrderFileGeneratedEvent;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use sales\services\pdf\GeneratorPdfService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use sales\dispatchers\EventDispatcher;
use modules\fileStorage\src\FileSystem;
use modules\fileStorage\src\entity\fileStorage\FileStorage;

/**
 * Class RentCarQuotePdfService
 */
class RentCarQuotePdfService
{
    public const TEMPLATE_KEY = 'pdf_car_rental';

    public static function processingFile(RentCarQuote $quote): bool
    {
        $patchToLocalFile = self::generateAsFile($quote);
        $projectKey = self::getProjectKey($quote);
        $clientId = self::getClientId($quote);
        $leadId = self::getLeadId($quote);

        $fileStorageRepository = Yii::createObject(FileStorageRepository::class);
        $fileClientRepository = Yii::createObject(FileClientRepository::class);
        $fileOrderRepository = Yii::createObject(FileOrderRepository::class);
        $fileLeadRepository = Yii::createObject(FileLeadRepository::class);
        $eventDispatcher = Yii::createObject(EventDispatcher::class);
        $fileSystem = Yii::createObject(FileSystem::class);

        $title = 'RentCarConfirmationQ' . self::getProductQuoteId($quote) . '.pdf';
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

        if ($fileStorage->fs_id && $orderId = ArrayHelper::getValue($quote, 'rcqProductQuote.pqOrder.or_id')) {
            $fileOrder = FileOrder::create(
                $fileStorage->fs_id,
                $orderId,
                $quote->rcq_product_quote_id,
                FileOrder::CATEGORY_CONFIRMATION
            );
            $fileOrderRepository->save($fileOrder);

            $eventDispatcher->dispatch(
                new OrderFileGeneratedEvent(
                    $orderId,
                    $fileStorage->fs_id,
                    OrderFileGeneratedEvent::TYPE_RENT_CAR_CONFIRMATION
                )
            );
        }
        if (file_exists($patchToLocalFile)) {
            FileHelper::unlink($patchToLocalFile);
        }
        return true;
    }

    /**
     * @param RentCarQuote $quote
     * @return mixed
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateForBrowserOutput(RentCarQuote $quote)
    {
        $content = self::getContent($quote);
        $fileName = 'rent_car_Q' . self::getProductQuoteId($quote) . '.pdf';

        return GeneratorPdfService::generateForBrowserOutput($content, $fileName);
    }

    /**
     * @param RentCarQuote $quote
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateAsFile(RentCarQuote $quote): string
    {
        $content = self::getContent($quote);
        $fileName = 'rent_car_Q' . self::getProductQuoteId($quote) . '.pdf';

        return GeneratorPdfService::generateAsFile($content, $fileName);
    }

    /**
     * @param RentCarQuote $quote
     * @return array
     */
    public static function getData(RentCarQuote $quote): array
    {
        $data['rent_car_quote'] = $quote->serialize();
        $data['project_key'] = self::getProjectKey($quote);
        $data['order'] = self::getOrderData($quote);
        return $data;
    }

    public static function getContent(RentCarQuote $quote): string
    {
        $data = self::getData($quote);
        $content = \Yii::$app->communication->getContent(self::TEMPLATE_KEY, $data);

        if ($content['error'] !== false) {
            throw new \RuntimeException(VarDumper::dumpAsString($content['error']));
        }
        return $content['content'];
    }

    private static function getOrderData(RentCarQuote $quote): ?array
    {
        if ($order = ArrayHelper::getValue($quote, 'rcqProductQuote.pqOrder')) {
            /** @var Order $order */
            return $order->serialize();
        }
        return null;
    }

    private static function getProductQuoteId(RentCarQuote $quote): string
    {
        return ArrayHelper::getValue($quote, 'rcqProductQuote.pq_id', '');
    }

    private static function getProjectKey(RentCarQuote $quote): string
    {
        return ArrayHelper::getValue($quote, 'rcqProductQuote.pqProduct.prLead.project.project_key', '');
    }

    private static function getClientId(RentCarQuote $quote): ?int
    {
        return ArrayHelper::getValue($quote, 'rcqProductQuote.pqProduct.prLead.client_id');
    }

    private static function getLeadId(RentCarQuote $quote): ?int
    {
        return ArrayHelper::getValue($quote, 'rcqProductQuote.pqProduct.pr_lead_id');
    }
}
