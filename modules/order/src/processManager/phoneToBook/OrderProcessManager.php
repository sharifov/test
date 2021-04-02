<?php

namespace modules\order\src\processManager\phoneToBook;

use modules\order\src\processManager\Status;
use modules\order\src\processManager\Type;
use sales\entities\EventTrait;
use modules\order\src\processManager\events;
use modules\order\src\processManager\OrderProcessManager as BaseProcessManager;

class OrderProcessManager extends BaseProcessManager
{
    use EventTrait;

    public static function create(int $orderId, \DateTimeImmutable $date): self
    {
        $process = new static();
        $process->opm_id = $orderId;
        $process->opm_status = Status::NEW;
        $process->opm_type = Type::PHONE_TO_BOOK;
        $process->opm_created_dt = $date->format('Y-m-d H:i:s');
        $process->recordEvent(new events\CreatedEvent($process, $date->format('Y-m-d H:i:s.u')));
        return $process;
    }

    public function bookingFlight(\DateTimeImmutable $date): void
    {
        if ($this->opm_status !== Status::NEW) {
            throw new \DomainException('OrderProcessManager is Not New. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = Status::BOOKING_FLIGHT;
        $this->recordEvent(new events\BookingFlightEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function bookingOtherProducts(\DateTimeImmutable $date): void
    {
        if ($this->opm_status !== Status::BOOKING_FLIGHT) {
            throw new \DomainException('OrderProcessManager is Not Booking Flight. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = Status::BOOKING_OTHER_PRODUCTS;
        $this->recordEvent(new events\BookingOtherProductsEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function booked(\DateTimeImmutable $date): void
    {
        if (!$this->isRunning()) {
            throw new \DomainException('OrderProcessManager is Not Running. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = Status::BOOKED;
        $this->recordEvent(new events\BookedEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function stop(\DateTimeImmutable $date): void
    {
        if ($this->isStopped()) {
            throw new \DomainException('OrderProcessManager is already Stopped. Id: ' . $this->opm_id);
        }
        $this->opm_status = Status::STOPPED;
        $this->recordEvent(new events\CanceledEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function isRunning(): bool
    {
        return $this->isBookingFlight() || $this->isOtherProductsBooking();
    }

    public function isBookingFlight(): bool
    {
        return $this->opm_status === Status::BOOKING_FLIGHT;
    }

    public function isOtherProductsBooking(): bool
    {
        return $this->opm_status === Status::BOOKING_OTHER_PRODUCTS;
    }

    public function isBooked(): bool
    {
        return $this->opm_status === Status::BOOKED;
    }

    public function isStopped(): bool
    {
        return $this->opm_status === Status::STOPPED;
    }
}
