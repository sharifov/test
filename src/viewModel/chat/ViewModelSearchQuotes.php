<?php

namespace src\viewModel\chat;

use common\models\Lead;
use src\dto\searchService\SearchServiceQuoteDTO;
use src\forms\api\searchQuote\FlightQuoteSearchForm;
use src\forms\lead\ItineraryEditForm;
use yii\data\ArrayDataProvider;

/**
 * Class ViewModelSearchQuotes
 * @package src\viewModel\chat
 *
 * @property Lead|null $lead
 * @property ItineraryEditForm|null $itineraryForm
 * @property ArrayDataProvider|null $dataProvider
 * @property FlightQuoteSearchForm|null $flightQuoteSearchForm
 * @property SearchServiceQuoteDTO|null $searchServiceDto
 * @property array $quotes
 * @property array $airlines
 * @property array $locations
 * @property string $keyCache
 * @property int|null $chatId
 * @property bool $leadCreated
 * @property string $flightRequestFormMode
 */
class ViewModelSearchQuotes
{
    public ?Lead $lead = null;

    public ?ItineraryEditForm $itineraryForm = null;

    public array $quotes = [];

    public ?ArrayDataProvider $dataProvider = null;

    public ?FlightQuoteSearchForm $flightQuoteSearchForm = null;

    public string $keyCache = '';

    public ?SearchServiceQuoteDTO $searchServiceDto = null;

    public array $airlines = [];

    public array $locations = [];

    public ?int $chatId = null;

    public bool $leadCreated = false;

    public string $flightRequestFormMode = 'view';
}
