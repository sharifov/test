<?php

namespace modules\objectTask\src\services;

use modules\objectTask\src\entities\ObjectTaskScenario;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ObjectTaskScenarioParameterService
{
    private ObjectTaskScenario $objectTaskScenario;

    public function __construct(ObjectTaskScenario $objectTaskScenario)
    {
        $this->objectTaskScenario = $objectTaskScenario;
    }

    public function get(string $key, mixed $defaultValue): mixed
    {
        $json = $this->objectTaskScenario->ots_data_json;

        if (is_string($json)) {
            $json = Json::decode($json);
        }

        return ArrayHelper::getValue($json, $key, $defaultValue);
    }
}
