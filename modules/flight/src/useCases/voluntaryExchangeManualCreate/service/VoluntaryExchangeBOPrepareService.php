<?php

namespace modules\flight\src\useCases\voluntaryExchangeManualCreate\service;

use common\models\Project;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class VoluntaryExchangeBOPrepareService
 *
 * @property string|null $apiKey
 * @property string|null $bookingId
 * @property array|null $tickets
 *
 * @property Project $project
 * @property ProductQuote $originProductQuote
 */
class VoluntaryExchangeBOPrepareService
{
    private ?string $apiKey = null;
    private ?string $bookingId = null;
    private ?array $tickets = null;

    private Project $project;
    private ProductQuote $originProductQuote;

    /**
     * @param Project $project
     * @param ProductQuote $originProductQuote
     */
    public function __construct(Project $project, ProductQuote $originProductQuote)
    {
        $this->project = $project;
        $this->originProductQuote = $originProductQuote;

        $this->fill();
    }

    private function fill(): void
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
        $this->bookingId = $this->originProductQuote->getLastBookingId();
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
}
