<?php

namespace src\services\abtesting;

class ABTestingBaseEntity
{
    private string $name;

    private float $expectedPercentage;

    private int $counter;

    private float $currentPercentage = 0;


    public function __construct(string $name, float $expectedPercentage, int $counter)
    {
        $this->name = $name;
        $this->expectedPercentage = $expectedPercentage;
        $this->counter = $counter;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * @return float
     */
    public function getExpectedPercentage(): float
    {
        return $this->expectedPercentage;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    /**
     * @return float|null
     */
    public function getCurrentPercentage(): ?float
    {
        return $this->currentPercentage;
    }

    public function calculateCurrentPercentage(int $totalCounter)
    {
        if ($totalCounter !== 0) {
            $this->currentPercentage = ($this->counter / $totalCounter) * 100;
        }
    }

    public function toArray(): array
    {
        return [
            'name'               => $this->name,
            'expectedPercentage' => $this->expectedPercentage,
            'counter'            => $this->counter,
            'currentPercentage'  => $this->currentPercentage
        ];
    }
}
