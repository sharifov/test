<?php

namespace modules\flight\src\dto\flightQuoteSearchDTO;

use modules\flight\models\Flight;
use yii\helpers\ArrayHelper;

class FlightQuoteSearchDTO
{
    public array $fl = [];

    public ?string $cabin = null;

    public ?string $cid = '';

    public ?int $adt = null;

    public ?int $chd = null;

    public ?int $inf = null;

    public bool $group = true;

    public $gdsCode;

    public bool $ngs = true;

    public function __construct(Flight $flight, $gdsCode = null)
    {
        if (!$flight->flightSegments) {
            throw new \DomainException('Flight request has no segments; Fill flight request data;');
        }

        $this->cabin = $flight->getCabinRealCode($flight->fl_cabin_class);
        $this->cid = $flight->flProduct->prLead->project->getAirSearchSid() ?? \Yii::$app->params['search']['sid'];
        $this->adt = $flight->fl_adults;
        $this->chd = $flight->fl_children;
        $this->inf = $flight->fl_infants;
        $this->ngs = true;

        if ($gdsCode) {
            $this->gdsCode = $gdsCode;
        }

        foreach ($flight->flightSegments as $segment) {
            $this->fl[] = [
                'o' => $segment->fs_origin_iata,
                'd' => $segment->fs_destination_iata,
                'dt' => $segment->fs_departure_date
            ];
        }
    }

    public function getAsArray(): array
    {
        return ArrayHelper::toArray($this);
    }
}
