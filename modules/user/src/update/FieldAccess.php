<?php

namespace modules\user\src\update;

use common\models\Employee;
use modules\user\src\abac\dto\UserAbacDto;
use modules\user\src\abac\UserAbacObject;

/**
 * Class FieldAccess
 *
 * @property Employee $user
 * @property bool $isNewRecord
 * @property array $cache
 */
class FieldAccess
{
    private Employee $user;
    private bool $isNewRecord;
    private array $cache = [
        'view' => [],
        'edit' => [],
    ];

    public function __construct(Employee $user, bool $isNewRecord)
    {
        $this->user = $user;
        $this->isNewRecord = $isNewRecord;
    }

    public function canShowProfileSettings(): bool
    {
        return $this->canShow('up_join_date')
            || $this->canShow('up_skill')
            || $this->canShow('up_call_type_id')
            || $this->canShow('up_2fa_secret')
            || $this->canShow('up_2fa_enable')
            || $this->canShow('up_telegram')
            || $this->canShow('up_telegram_enable')
            || $this->canShow('up_auto_redial')
            || $this->canShow('up_kpi_enable')
            || $this->canShow('up_show_in_contact_list')
            || $this->canShow('up_call_recording_disabled');
    }

    public function canShow(string $field): bool
    {
        return $this->canView($field) || $this->canEdit($field);
    }

    public function canView(string $field): bool
    {
        if (array_key_exists($field, $this->cache['view'])) {
            return $this->cache['view'][$field];
        }
        $userAbacDto = new UserAbacDto($field);
        $userAbacDto->isNewRecord = $this->isNewRecord;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User field view*/
        $this->cache['view'][$field] = \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->user);
        return $this->cache['view'][$field];
    }

    public function canEdit(string $field): bool
    {
        if (array_key_exists($field, $this->cache['edit'])) {
            return $this->cache['edit'][$field];
        }
        $userAbacDto = new UserAbacDto($field);
        $userAbacDto->isNewRecord = $this->isNewRecord;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User field edit*/
        $this->cache['edit'][$field] = \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->user);
        return $this->cache['edit'][$field];
    }
}
