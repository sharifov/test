<?php

namespace src\dto\flightQuote;

/**
 * Class UnUsedSegmentDTO
 * @package src\dto\fliqhtQupte
 *
 * @property int|null $caseId
 * @property int|null $projectId
 * @property string|null $projectKey
 * @property int|null $productQuoteChangeId
 * @property int $productQuoteId
 * @property int $flightQuoteSegmentId
 * @property string $departureDt
 */
class UnUsedSegmentDTO
{
    public ?int $caseId;
    public ?int $projectId;
    public ?string $projectKey;
    public ?int $productQuoteChangeId;
    public int $productQuoteId;
    public int $flightQuoteSegmentId;
    public string $departureDt;

    public function __construct(array $segmentData)
    {
        $this->caseId = $segmentData['caseId'];
        $this->projectId = $segmentData['projectId'];
        $this->projectKey = $segmentData['projectKey'];
        $this->productQuoteChangeId = $segmentData['productQuoteChangeId'];
        $this->productQuoteId = $segmentData['productQuoteId'];
        $this->flightQuoteSegmentId = $segmentData['flightQuoteSegmentId'];
        $this->departureDt = $segmentData['departureDt'];
    }
}
