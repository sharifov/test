<?php

namespace modules\objectTask\src\scenarios;

use modules\objectTask\src\entities\ObjectTaskScenario;

abstract class BaseScenario
{
    protected ?ObjectTaskScenario $objectTaskScenario = null;

    public function __construct()
    {
        $this->objectTaskScenario = ObjectTaskScenario::find()
            ->where([
                'ots_key' => static::KEY,
            ])
            ->limit(1)
            ->one();
    }

    abstract public function process(): void;

    public function getConfig(): array
    {
        return $this->objectTaskScenario->ots_data_json ?? [];
    }

    public function getConfigParameter(string $key, mixed $defaultValue = null): mixed
    {
        return \yii\helpers\ArrayHelper::getValue(
            $this->getConfig(),
            $key,
            $defaultValue
        );
    }
}
