<?php

namespace sales\dto\searchService;

use common\components\SearchService;
use common\models\Lead;
use yii\helpers\ArrayHelper;

/**
 * Class SearchServiceQuoteDTO
 * @package sales\dto\searchService
 *
 * @property Lead $lead
 * @property array $fl
 * @property string|null $cabin
 * @property string|null $cid
 * @property int|null $adt
 * @property int|null $chd
 * @property int|null $inf
 * @property bool $group
 * @property int|null $limit
 * @property mixed $gdsCode
 * @property mixed $ppn
 */
class SearchServiceQuoteDTO
{
    private Lead $lead;

    public array $fl = [];

    public ?string $cabin = null;

    public ?string $cid = '';

    public ?int $adt = null;

    public ?int $chd = null;

    public ?int $inf = null;

    public bool $group = true;

    public ?int $limit = null;

    public $gdsCode;

    public $ppn;

    public function __construct(Lead $lead, int $limit = 600, $gdsCode = null, bool $group = true)
    {
        $this->lead = $lead;

        $this->cabin = SearchService::getCabinRealCode($lead->cabin);

        $sid = $lead->project->airSearchSid ?? null;
        $this->cid = $sid ?: \Yii::$app->params['search']['sid'];
        $this->adt = $lead->adults;
        $this->chd = $lead->children;
        $this->inf = $lead->infants;
        $this->group = $group;

        if ($limit) {
            $this->limit = $limit;
        }

        if ($gdsCode) {
            $this->gdsCode = $gdsCode;
        }

        foreach ($lead->leadFlightSegments as $flightSegment) {
            $segment = [
                'o' => $flightSegment->origin,
                'd' => $flightSegment->destination,
                'dt' => $flightSegment->departure
            ];

            if ($flightSegment->flexibility > 0) {
                $segment['flex'] = $flightSegment->flexibility;

                if ($flightSegment->flexibility_type && $flexType = SearchService::getSearchFlexType($flightSegment->flexibility_type)) {
                    $segment['ft'] = $flexType;
                }
            }

            $this->fl[] = $segment;
        }

        if ($lead->client->isExcluded()) {
            $this->ppn = $lead->client->cl_ppn;
        }
    }

    public function getAsArray(): array
    {
        return ArrayHelper::toArray($this);
    }

    public function getLeadId(): int
    {
        return $this->lead->id;
    }
}
