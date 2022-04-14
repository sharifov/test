<?php

namespace src\model\priceResearchLink\service;

use common\models\Lead;
use src\forms\lead\ItineraryEditForm;
use src\forms\lead\SegmentEditForm;
use src\forms\siteSetting\PriceResearchLinkForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class PriceResearchLinkService
{
    private PriceResearchLinkForm $priceResearchLinkForm;

    private ItineraryEditForm $itineraryEditForm;

    public function __construct(PriceResearchLinkForm $priceResearchLinkForm, ItineraryEditForm $itineraryEditForm)
    {
        $this->priceResearchLinkForm = $priceResearchLinkForm;
        $this->itineraryEditForm     = $itineraryEditForm;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function generateUrl(): string
    {
        $url = '';
        switch ($this->itineraryEditForm->tripType) {
            case Lead::TRIP_TYPE_ONE_WAY:
                $url = $this->generateOneTripUrl();
                break;
            case Lead::TRIP_TYPE_ROUND_TRIP:
                $url = $this->generateRoundTripUrl();
                break;
            case Lead::TRIP_TYPE_MULTI_DESTINATION:
                $url = $this->generateMultiCityUrl();
                break;
        }
        return $url;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function generateOneTripUrl(): string
    {
        $types = ArrayHelper::toArray($this->priceResearchLinkForm->types);
        $rawUrl = $this->priceResearchLinkForm->url . ArrayHelper::getValue($types, 'oneTrip');
        /* @var SegmentEditForm $flightSegment */
        $flightSegment = $this->itineraryEditForm->segments[0];
        $departureDate = $this->formatDate($flightSegment->departure, $this->priceResearchLinkForm->dateFormat);
        $rawUrl        = str_replace('{%origin%}', $flightSegment->origin, $rawUrl);
        $rawUrl        = str_replace('{%destination%}', $flightSegment->destination, $rawUrl);
        $rawUrl        = str_replace('{%departure_date%}', $departureDate, $rawUrl);
        return $this->replaceNonItineraryParamsInUrl($rawUrl);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function generateRoundTripUrl(): string
    {
        $types = ArrayHelper::toArray($this->priceResearchLinkForm->types);
        $rawUrl = $this->priceResearchLinkForm->url .  ArrayHelper::getValue($types, 'roundTrip');
        /* @var SegmentEditForm $flightSegment */
        $firstFlightSegment  = $this->itineraryEditForm->segments[0];
        $secondFlightSegment = $this->itineraryEditForm->segments[1];
        $firstDepartureDate  = $this->formatDate(
            $firstFlightSegment->departure,
            $this->priceResearchLinkForm->dateFormat
        );
        $secondDepartureDate = $this->formatDate(
            $secondFlightSegment->departure,
            $this->priceResearchLinkForm->dateFormat
        );
        $rawUrl              = str_replace('{%origin%}', $firstFlightSegment->origin, $rawUrl);
        $rawUrl              = str_replace('{%destination%}', $firstFlightSegment->destination, $rawUrl);
        $rawUrl              = str_replace('{%first_departure_date%}', $firstDepartureDate, $rawUrl);
        $rawUrl              = str_replace('{%second_departure_date%}', $secondDepartureDate, $rawUrl);
        return $this->replaceNonItineraryParamsInUrl($rawUrl);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function generateMultiCityUrl(): string
    {
        $types = ArrayHelper::toArray($this->priceResearchLinkForm->types);
        $rawUrl = $this->priceResearchLinkForm->url .  ArrayHelper::getValue($types, 'multiCity');
        $segments = $this->itineraryEditForm->segments;
        /* @var SegmentEditForm $segment */
        $itineraryPart = '';
        foreach ($segments as $key => $segment) {
            $segmentItineraryPart = $this->priceResearchLinkForm->multiCityItineraryPattern;
            $segmentItineraryPart = str_replace('{%origin%}', $segment->origin, $segmentItineraryPart);
            $segmentItineraryPart = str_replace('{%destination%}', $segment->destination, $segmentItineraryPart);
            $departureDate        = $this->formatDate(
                $segment->departure,
                $this->priceResearchLinkForm->multiCityDateFormat
            );
            $segmentItineraryPart = str_replace('{%departure_date%}', $departureDate, $segmentItineraryPart);
            $segmentItineraryPart = str_replace('{%segment_index%}', $key, $segmentItineraryPart);
            $itineraryPart        .= $segmentItineraryPart;
        }
        $rawUrl = str_replace('{%itinerary_part%}', $itineraryPart, $rawUrl);
        return $this->replaceNonItineraryParamsInUrl($rawUrl);
    }

    /**
     * @param $date
     * @param $dateFormat
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function formatDate($date, $dateFormat): string
    {
        return \Yii::$app->formatter->asDate($date, $dateFormat);
    }


    /**
     * @param string $url
     * @return string
     * @throws \Exception
     */
    private function replaceNonItineraryParamsInUrl(string $url): string
    {
        $cabinTypeReplace = ArrayHelper::getValue(
            $this->priceResearchLinkForm->cabinClassMappings,
            $this->itineraryEditForm->cabin
        );

        $url = str_replace('{%cabin_type%}', $cabinTypeReplace, $url);
        $url = str_replace('{%adults_count%}', $this->itineraryEditForm->adults, $url);

        if ((int)$this->itineraryEditForm->children || (int)$this->itineraryEditForm->infants) {
            if ($this->priceResearchLinkForm->childrenParameterType === PriceResearchLinkForm::CHILDREN_PARAMETER_TYPE_ENUMERABLE) {
                $childrenSubQuery            = $this->priceResearchLinkForm->childrenSubQueryPart;
                $childrenCountPart           = str_repeat(
                    $this->priceResearchLinkForm->childPaxTypeEnumerableParameter . $this->priceResearchLinkForm->childrenParameterSeparator,
                    $this->itineraryEditForm->children
                );
                $infantsCountPart            = str_repeat(
                    $this->priceResearchLinkForm->infantPaxTypeEnumerableParameter . $this->priceResearchLinkForm->childrenParameterSeparator,
                    $this->itineraryEditForm->infants
                );
                $childrenAndInfantsCountPart = $childrenCountPart . $infantsCountPart;
                $childrenAndInfantsCountPart = substr_replace($childrenAndInfantsCountPart, "", -1);

                $childrenSubQuery = str_replace(
                    '{%children_and_infants_count_part%}',
                    $childrenAndInfantsCountPart,
                    $childrenSubQuery
                );

                $url = str_replace('{%children_sub_query%}', $childrenSubQuery, $url);
            }
            if ($this->priceResearchLinkForm->childrenParameterType === PriceResearchLinkForm::CHILDREN_PARAMETER_TYPE_QUANTITATIVE) {
                $childrenSubQuery = $this->priceResearchLinkForm->childrenSubQueryPart;
                $childrenSubQuery = str_replace(
                    '{%children_count%}',
                    $this->itineraryEditForm->children,
                    $childrenSubQuery
                );
                $childrenSubQuery = str_replace(
                    '{%infants_count%}',
                    $this->itineraryEditForm->infants,
                    $childrenSubQuery
                );
                $url              = str_replace('{%children_sub_query%}', $childrenSubQuery, $url);
            }
        } else {
            $url = str_replace('{%children_sub_query%}', '', $url);
        }
        return $url;
    }
}
