<?php

namespace modules\flight\src\useCases\voluntaryExchangeManualCreate\service;

use common\models\Project;
use modules\product\src\entities\productQuote\ProductQuote;
use src\entities\cases\Cases;

/**
 * Class VoluntaryExchangeBOPrepareService
 *
 * @property string|null $apiKey
 * @property string|null $bookingId
 * @property array|null $tickets
 *
 * @property Project $project
 * @property ProductQuote $originProductQuote
 * @property Cases|null $case
 */
class VoluntaryExchangeBOPrepareService
{
    private ?string $apiKey = null;
    private ?string $bookingId = null;
    private ?array $tickets = null;
    private ?bool $statusFill = null;

    private Project $project;
    private ProductQuote $originProductQuote;
    private ?Cases $case = null;

    /**
     * @param Project $project
     * @param ProductQuote $originProductQuote
     */
    public function __construct(Project $project, ProductQuote $originProductQuote, ?Cases $case = null)
    {
        $this->project = $project;
        $this->originProductQuote = $originProductQuote;
        $this->case = $case;
    }

    public function fill(): void
    {
        if (empty($this->project->api_key)) {
            throw new \RuntimeException('Api key is empty. Project(' . $this->project->project_key . ')');
        }
        if (!$this->originProductQuote->isFlight()) {
            throw new \RuntimeException('OriginProductQuote must type Flight. Gid(' . $this->originProductQuote->pq_gid . ')');
        }
        if (!$flightPaxes = $this->originProductQuote->flightQuote->fqFlight->flightPaxes ?? null) {
            throw new \RuntimeException('FlightPaxes not found');
        }

        foreach ($flightPaxes as $key => $flightPax) {
            if ($ticketNumber = $flightPax->flightQuoteTicket->fqt_ticket_number ?? null) {
                $this->tickets[] = $ticketNumber;
            }
        }
        if (empty($this->tickets)) {
            throw new \RuntimeException('TicketNumbers not found');
        }

        $this->apiKey = $this->project->api_key;

        if (!$this->bookingId = $this->originProductQuote->getLastBookingId()) {
            $this->bookingId = $this->case->cs_order_uid;
        }
        if (!$this->bookingId) {
            throw new \RuntimeException('BookingId not found in OriginProductQuote Gid(' . $this->originProductQuote->pq_gid . ')');
        }
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function getBookingId(): ?string
    {
        return $this->bookingId;
    }

    public function getTickets(): ?array
    {
        return $this->tickets;
    }

    public function getStatusFill(): ?bool
    {
        return $this->statusFill;
    }
}
