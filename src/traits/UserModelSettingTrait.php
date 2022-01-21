<?php

namespace src\traits;

use common\models\Employee;
use yii\helpers\ArrayHelper;

/**
 * Trait UserModelSettingTrait
 *
 * @property array $fields
 * @property Employee $currentUser
 */
trait UserModelSettingTrait
{
    public $fields = [];
    public $currentUser;

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getCurrentUser(): Employee
    {
        return $this->currentUser;
    }

    public function setCurrentUser(Employee $currentUser): void
    {
        $this->currentUser = $currentUser;
    }

    public function isFieldShow(string $fieldName): bool
    {
        return ArrayHelper::isIn($fieldName, $this->fields);
    }
}
