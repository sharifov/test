<?php

namespace modules\cruise\src\entity\cruiseQuote\serializer;

use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use sales\entities\serializer\Serializer;
use yii\helpers\ArrayHelper;

/**
 * Class CruiseQuoteSerializer
 *
 * @property CruiseQuote $model
 */
class CruiseQuoteSerializer extends Serializer
{
    public function __construct(CruiseQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [];
    }

    public function getData(): array
    {
        $data = [];

        if ($this->model->crq_data_json) {
            $data['cruiseLine'] = [
                'code' => $this->model->crq_data_json['cruiseLine']['code'],
                'name' => $this->model->crq_data_json['cruiseLine']['name'],
            ];
            $data['departureDate'] = $this->model->crq_data_json['departureDate'];
            $data['returnDate'] = $this->model->crq_data_json['returnDate'];
            $data['destination'] = $this->model->crq_data_json['itinerary']['destination']['destination'];
            $data['subDestination'] = $this->model->crq_data_json['itinerary']['destination']['subDestination'];
            $data['ship'] = [
                'code' => $this->model->crq_data_json['ship']['code'],
                'name' => $this->model->crq_data_json['ship']['name'],
            ];
            $data['cabin'] = $this->model->crq_data_json['cabin'];
            $data['adults'] = $this->model->getAdults();
            $data['children'] = $this->model->getChildren();

            $data['destinationName'] = ArrayHelper::getValue((array) $this->model->crq_data_json, 'itinerary.locations.0.location.name');

            $data['crq_data_json'] = $this->model->crq_data_json;
        }

        return $data;
    }
}
