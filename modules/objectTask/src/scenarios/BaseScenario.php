<?php

namespace modules\objectTask\src\scenarios;

use modules\objectTask\src\entities\ObjectTaskScenario;
use modules\objectTask\src\scenarios\statements\BaseObject;
use src\access\ConditionExpressionService;

abstract class BaseScenario
{
    protected ObjectTaskScenario $objectTaskScenario;
    protected mixed $object;

    public function __construct(ObjectTaskScenario $objectTaskScenario, mixed $object)
    {
        $this->objectTaskScenario = $objectTaskScenario;
        $this->object = $object;
    }

    abstract public function getStatementDTO();

    abstract public static function getStatementObject(): BaseObject;

    abstract public function process(): void;

    abstract public function getObject();

    abstract public static function getTemplate(): array;

    public function canProcess(): bool
    {
        return ConditionExpressionService::isValidCondition(
            $this->objectTaskScenario->ots_condition,
            [
                static::OBJECT => static::getStatementDTO()
            ]
        );
    }

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

    protected function isEnable(): bool
    {
        return (bool)$this->objectTaskScenario->ots_enable;
    }
}
