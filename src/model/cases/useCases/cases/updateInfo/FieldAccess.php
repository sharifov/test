<?php

namespace src\model\cases\useCases\cases\updateInfo;

use common\models\Employee;
use modules\cases\src\abac\update\UpdateAbacDto;
use modules\cases\src\abac\update\UpdateAbacObject;
use src\entities\cases\Cases;
use Yii;

/**
 * Class FieldAccess
 *
 * @property Employee $user
 * @property bool $isNewRecord
 */
class FieldAccess
{
    private Employee $user;
    private Cases $case;

    public function __construct(Employee $user, Cases $case)
    {
        $this->user = $user;
        $this->case = $case;
    }

    public function canEditDepartment(): bool
    {
        /** @abac $caseAbacDto, UpdateAbacObject::UI_BLOCK_SALE_LIST, UpdateAbacObject::ACTION_EDIT_DEPARTMENT, Restrict access to edit department */
        return Yii::$app->abac->can(new UpdateAbacDto($this->case), UpdateAbacObject::UI_BLOCK_UPDATE_LIST, UpdateAbacObject::ACTION_EDIT_DEPARTMENT, $this->user);
    }

    public function canEditCategory(): bool
    {
        /** @abac $caseAbacDto, UpdateAbacObject::UI_BLOCK_SALE_LIST, UpdateAbacObject::ACTION_EDIT_CATEGORY, Restrict access to edit category */
        return Yii::$app->abac->can(new UpdateAbacDto($this->case), UpdateAbacObject::UI_BLOCK_UPDATE_LIST, UpdateAbacObject::ACTION_EDIT_CATEGORY, $this->user);
    }

    public function canEditDescription(): bool
    {
        /** @abac $caseAbacDto, UpdateAbacObject::UI_BLOCK_SALE_LIST, UpdateAbacObject::ACTION_EDIT_DESCRIPTION, Restrict access to edit description */
        return Yii::$app->abac->can(new UpdateAbacDto($this->case), UpdateAbacObject::UI_BLOCK_UPDATE_LIST, UpdateAbacObject::ACTION_EDIT_DESCRIPTION, $this->user);
    }
}
