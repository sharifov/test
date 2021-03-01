<?php

namespace modules\order\src\processManager;

use sales\entities\EventTrait;
use modules\order\src\processManager\events;
use yii\db\ActiveRecord;

/**
 * Class OrderProcessManager
 *
 * @property $opm_id
 * @property $opm_status
 * @property $opm_created_dt
 */
class OrderProcessManager extends ActiveRecord
{
    use EventTrait;

    public const STATUS_NEW = 1;
    public const STATUS_BOOKING_FLIGHT = 2;
    public const STATUS_BOOKING_OTHER_PRODUCTS = 3;
    public const STATUS_BOOKED = 10;
    public const STATUS_FAILED = 11;
    public const STATUS_CANCELED = 12;

    public const STATUS_LIST = [
        self::STATUS_NEW => 'New',
        self::STATUS_BOOKING_FLIGHT => 'Booking flight',
        self::STATUS_BOOKING_OTHER_PRODUCTS => 'Booking other products',
        self::STATUS_BOOKED => 'Booked',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_CANCELED => 'Canceled',
    ];

    public static function create(int $orderId, \DateTimeImmutable $date): self
    {
        $process = new static();
        $process->opm_id = $orderId;
        $process->opm_status = self::STATUS_NEW;
        $process->opm_created_dt = $date->format('Y-m-d H:i:s');
        $process->recordEvent(new events\CreatedEvent($process, $date->format('Y-m-d H:i:s.u')));
        return $process;
    }

    public function bookingFlight(\DateTimeImmutable $date): void
    {
        if ($this->opm_status !== self::STATUS_NEW) {
            throw new \DomainException('OrderProcessManager is Not New. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = self::STATUS_BOOKING_FLIGHT;
        $this->recordEvent(new events\BookingFlightEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function bookingOtherProducts(\DateTimeImmutable $date): void
    {
        if ($this->opm_status !== self::STATUS_BOOKING_FLIGHT) {
            throw new \DomainException('OrderProcessManager is Not Booking Flight. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = self::STATUS_BOOKING_OTHER_PRODUCTS;
        $this->recordEvent(new events\BookingOtherProductsEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function booked(\DateTimeImmutable $date): void
    {
        if (!$this->isRunning()) {
            throw new \DomainException('OrderProcessManager is Not Running. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = self::STATUS_BOOKED;
        $this->recordEvent(new events\BookedEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function failed(\DateTimeImmutable $date): void
    {
        if (!$this->isRunning()) {
            throw new \DomainException('OrderProcessManager is Not Running. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = self::STATUS_FAILED;
        $this->recordEvent(new events\FailedEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function cancel(\DateTimeImmutable $date): void
    {
        if (!$this->isRunning()) {
            throw new \DomainException('OrderProcessManager is Not Running. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = self::STATUS_CANCELED;
        $this->recordEvent(new events\CanceledEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function isRunning(): bool
    {
        return $this->isBookingFlight() || $this->isOtherProductsBooking();
    }

    public function isBookingFlight(): bool
    {
        return $this->opm_status === self::STATUS_BOOKING_FLIGHT;
    }

    public function isOtherProductsBooking(): bool
    {
        return $this->opm_status === self::STATUS_BOOKING_OTHER_PRODUCTS;
    }

    public function isBooked(): bool
    {
        return $this->opm_status === self::STATUS_BOOKED;
    }
}
