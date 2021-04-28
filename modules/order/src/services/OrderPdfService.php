<?php

namespace modules\order\src\services;

use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\processingPdf\PdfBaseService;

/**
 * Class OrderPdfService
 */
class OrderPdfService extends PdfBaseService
{
    public $fileOrderCategory = FileOrder::CATEGORY_RECEIPT;
    public $templateKey = 'pdf_order_receipt';
    public $eventType = OrderFileGeneratedEvent::TYPE_ORDER_RECEIPT;

    public function fillData()
    {
        $this->communicationData['project_key'] = $this->projectKey;
        $this->communicationData['order'] = $this->object->getOrder() ? $this->object->getOrder()->serialize() : null;
        return $this;
    }

    public function processingFileWithoutEvent(): bool
    {
        $patchToLocalFile = $this->generateAsFile();

        $fileStorageId = $this->fileStorage($patchToLocalFile);
        if ($this->clientId) {
            $this->fileToClient($fileStorageId);
        }
        if ($this->leadId) {
            $this->fileToLead($fileStorageId);
        }
        if ($this->orderId) {
            $this->fileToOrder($fileStorageId);
        }
        $this->unlinkLocalFile($patchToLocalFile);

        return true;
    }
}
