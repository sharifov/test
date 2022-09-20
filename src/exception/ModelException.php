<?php

namespace src\exception;

use src\helpers\ErrorsToStringHelper;
use Throwable;
use yii\base\Model;

class ModelException extends \DomainException
{
    protected Model $model;
    protected string $defaultMessage;
    protected ?string $customMessage;
    protected ?array $keyModelData;

    protected array $additionalData = [];

    public function __construct(
        Model $model,
        ?array $keyModelData = null,
        ?string $customMessage = null,
        array $extraData = [],
        string $defaultMessage = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->model = $model;
        $this->customMessage = $customMessage;
        $this->defaultMessage = $defaultMessage;
        $this->keyModelData = $keyModelData;

        //$this->modelData = $additionalData;

        parent::__construct($this->generateMessage(), $code, $previous);
    }

    protected function generateMessage(): string
    {
        if ($this->customMessage) {
            return $this->customMessage;
        }
        if (!$this->model->hasErrors()) {
            return $this->defaultMessage;
        }
        return ErrorsToStringHelper::extractFromModel($this->model, ' ');
    }

    public function generateAdditionalData(): array
    {
        if ($this->keyModelData) {
            foreach ($this->keyModelData as $key) {
                $this->additionalData[$key] = $this->model->{$key} ?? null;
            }
        }

        // $extraData
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}
