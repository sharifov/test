<?php

namespace src\dto\searchService;

use common\components\SearchService;
use common\models\Currency;
use common\models\Lead;
use common\models\LeadFlightSegment;
use yii\helpers\ArrayHelper;

/**
 * Class SearchServiceQuoteDTO
 * @package src\dto\searchService
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
    private ?Lead $lead = null;
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
    public string $currency = Currency::DEFAULT_CURRENCY;

    public function __construct(?Lead $lead, int $limit = 600, $gdsCode = null, bool $group = true)
    {
        $this->group = $group;
        $this->cid = \Yii::$app->params['search']['sid'];
        if ($limit) {
            $this->limit = $limit;
        }

        if ($gdsCode) {
            $this->gdsCode = $gdsCode;
        }

        if ($lead) {
            $this->lead = $lead;

            $this->cabin = SearchService::getCabinRealCode($lead->cabin);

            $sid = $lead->project->airSearchCid ?? null;
            $this->cid = $sid ?: $this->cid;
            $this->adt = $lead->adults;
            $this->chd = $lead->children;
            $this->inf = $lead->infants;


            $flightSegments = $lead->getleadFlightSegments()->orderBy(['departure' => SORT_ASC])->all();
            /** @var LeadFlightSegment $flightSegment */
            foreach ($flightSegments as $flightSegment) {
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

            if ($currency = $lead->leadPreferences->prefCurrency->cur_code ?? null) {
                $this->currency = $currency;
            }
        }
    }

    public function getAsArray(): array
    {
        return ArrayHelper::toArray($this);
    }

    public function getLeadId(): int
    {
        return $this->lead->id ?? 0;
    }

    public function setCid(string $cid): SearchServiceQuoteDTO
    {
        $this->cid = $cid;
        return $this;
    }
}
