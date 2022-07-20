<?php

namespace src\services\pdf\processingPdf;

use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\entity\fileOrder\FileOrderRepository;
use modules\fileStorage\src\entity\fileProductQuote\FileProductQuote;
use modules\fileStorage\src\entity\fileProductQuote\FileProductQuoteRepository;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\FileSystem;
use modules\fileStorage\src\services\CreateByLocalFileDto;
use modules\order\src\events\OrderFileGeneratedEvent;
use modules\product\src\interfaces\ProductDataInterface;
use src\dispatchers\EventDispatcher;
use src\services\pdf\GeneratorPdfService;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

/**
 * Class PdfBaseService
 * @property ProductDataInterface $object
 * @property int|null $leadId
 * @property int|null $orderId
 * @property int|null $clientId
 * @property string $projectKey
 * @property string $templateKey
 * @property int|null $productQuoteId
 * @property array|null $communicationData
 * @property string $eventType
 * @property string $fileOrderCategory
 *
 * @property string $extension
 * @property string|null $fileName
 * @property string|null $fileTitle
 *
 * @property FileProductQuoteRepository $fileProductQuoteRepository
 * @property FileStorageRepository $fileStorageRepository
 * @property FileClientRepository $fileClientRepository
 * @property FileOrderRepository $fileOrderRepository
 * @property FileLeadRepository $fileLeadRepository
 * @property EventDispatcher $eventDispatcher
 * @property FileSystem $fileSystem
 */
abstract class PdfBaseService
{
    public $object;
    public $leadId;
    public $orderId;
    public $clientId;
    public $eventType;
    public $projectKey;
    public $templateKey;
    public $fileOrderCategory = FileOrder::CATEGORY_CONFIRMATION;

    public ?int $productQuoteId = null;
    public ?array $communicationData = null;

    private FileProductQuoteRepository $fileProductQuoteRepository;
    private FileStorageRepository $fileStorageRepository;
    private FileClientRepository $fileClientRepository;
    private FileOrderRepository $fileOrderRepository;
    private FileLeadRepository $fileLeadRepository;
    private EventDispatcher $eventDispatcher;
    private FileSystem $fileSystem;

    private ?string $fileName = null;
    private ?string $fileTitle = null;
    private string $extension = 'pdf';

    public function __construct(ProductDataInterface $object)
    {
        $this->object = $object;
        $this->leadId = $object->getLead() ? $object->getLead()->id : null;
        $this->clientId = $object->getClient() ? $object->getClient()->id : null;
        $this->projectKey = $object->getProject()->project_key;
        $this->orderId = $object->getOrder() ? $object->getOrder()->or_id : null;

        $this->fileProductQuoteRepository = Yii::createObject(FileProductQuoteRepository::class);
        $this->fileStorageRepository = Yii::createObject(FileStorageRepository::class);
        $this->fileClientRepository = Yii::createObject(FileClientRepository::class);
        $this->fileOrderRepository = Yii::createObject(FileOrderRepository::class);
        $this->fileLeadRepository = Yii::createObject(FileLeadRepository::class);
        $this->eventDispatcher = Yii::createObject(EventDispatcher::class);
        $this->fileSystem = Yii::createObject(FileSystem::class);

        $this->fillData();
    }

    public function processingFile(): bool
    {
        $patchToLocalFile = $this->generateAsFile();
        $fileStorageId = $this->fileStorage($patchToLocalFile);
        if ($this->clientId) {
            $this->fileToClient($fileStorageId);
        }
        if ($this->getProductQuoteId()) {
            $this->fileToProductQuote($fileStorageId);
        }
        if ($this->orderId) {
            $this->fileToOrder($fileStorageId);
            $this->dispatchEvent($fileStorageId);
        }
        $this->unlinkLocalFile($patchToLocalFile);

        return true;
    }

    public function generateAsFile(): string
    {
        return GeneratorPdfService::generateAsFile($this->generateContent(), $this->getName());
    }

    public function generateForBrowserOutput()
    {
        return GeneratorPdfService::generateForBrowserOutput($this->generateContent(), $this->getName());
    }

    public function fillData()
    {
        //$this->communicationData = null; // custom realization in children class
        return $this;
    }

    public function generateContent(): string
    {
        if ($this->communicationData === null) {
            throw new \RuntimeException('CommunicationData cannot be empty');
        }
        $content = \Yii::$app->comms->getContent($this->templateKey, $this->communicationData);
        if ($content['error'] !== false) {
            throw new \RuntimeException(VarDumper::dumpAsString($content['error']));
        }
        return $content['content'];
    }

    public function getProductQuoteId(): ?int
    {
        return $this->productQuoteId;
    }

    public function setProductQuoteId(?int $productQuoteId): self
    {
        $this->productQuoteId = $productQuoteId;
        return $this;
    }

    public function unlinkLocalFile(string $patchToLocalFile): void
    {
        if (file_exists($patchToLocalFile)) {
            FileHelper::unlink($patchToLocalFile);
        }
    }

    public function fileToOrder(int $fileStorageId): void
    {
        $this->fileOrderRepository->save(
            FileOrder::create(
                $fileStorageId,
                $this->orderId,
                $this->getProductQuoteId(),
                $this->fileOrderCategory
            )
        );
    }

    private function dispatchEvent(int $fileStorageId): void
    {
        $this->eventDispatcher->dispatch(
            new OrderFileGeneratedEvent(
                $this->orderId,
                $fileStorageId,
                $this->eventType
            )
        );
    }

    public function fileToClient(int $fileStorageId): void
    {
        $this->fileClientRepository->save(FileClient::create($fileStorageId, $this->clientId));
    }

    public function fileToLead(int $fileStorageId): void
    {
        $this->fileLeadRepository->save(FileLead::create($fileStorageId, $this->leadId));
    }

    private function fileToProductQuote(int $fileStorageId): void
    {
        $this->fileProductQuoteRepository->save(FileProductQuote::create($fileStorageId, $this->getProductQuoteId()));
    }

    public function fileStorage($patchToLocalFile)
    {
        $createDto = new CreateByLocalFileDto($patchToLocalFile, $this->clientId, $this->projectKey, $this->getTitle(), $this->orderId);
        $fileStorage = FileStorage::createByLocalFile($createDto);

        $this->fileSystem->write($fileStorage->fs_path, file_get_contents($patchToLocalFile));
        $this->fileStorageRepository->save($fileStorage);

        return $fileStorage->fs_id;
    }

    public function getName(): string
    {
        if ($this->fileName) {
            return $this->fileName . '.' . $this->getExtension();
        }
        return $this->templateKey . '_' . $this->object->getId() . '.' . $this->getExtension();
    }

    private function getTitle(): string
    {
        if ($this->fileTitle) {
            return $this->fileTitle;
        }
        return Inflector::camelize($this->templateKey) . '-' . $this->object->getId() . '.' . $this->getExtension();
    }

    public function getCommunicationData(): ?array
    {
        return $this->communicationData;
    }

    /**
     * @param int|null $leadId
     * @return PdfBaseService
     */
    public function setLeadId(?int $leadId): PdfBaseService
    {
        $this->leadId = $leadId;
        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setFileName(?string $fileName): PdfBaseService
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function setFileTitle(?string $fileTitle): PdfBaseService
    {
        $this->fileTitle = $fileTitle;
        return $this;
    }
}
