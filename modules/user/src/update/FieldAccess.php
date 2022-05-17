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

    public function canEditOneOfMultipleFields(): bool
    {
        return $this->canEdit('user_departments')
            || $this->canEdit('form_roles')
            || $this->canEdit('status')
            || $this->canEdit('up_work_start_tm')
            || $this->canEdit('up_work_minutes')
            || $this->canEdit('up_timezone')
            || $this->canEdit('up_inbox_show_limit_leads')
            || $this->canEdit('up_default_take_limit_leads')
            || $this->canEdit('client_chat_user_channel')
            || $this->canEdit('up_min_percent_for_take_leads')
            || $this->canEdit('up_frequency_minutes')
            || $this->canEdit('up_base_amount')
            || $this->canEdit('up_commission_percent')
            || $this->canEdit('up_call_expert_limit')
            || $this->canEdit('up_auto_redial')
            || $this->canEdit('up_kpi_enable')
            || $this->canEdit('up_leaderboard_enabled');
    }

    public function canShowProfileWithParameters(): bool
    {
        return $this->canShow('username')
            || $this->canShow('email')
            || $this->canShow('full_name')
            || $this->canShow('password')
            || $this->canShow('nickname')
            || $this->canShow('form_roles')
            || $this->canShow('status')
            || $this->canShow('user_groups')
            || $this->canShow('user_projects')
            || $this->canShow('user_departments')
            || $this->canShow('client_chat_user_channel')
            || $this->canShow('user_shift_assigns')
            || $this->canShow('up_work_start_tm')
            || $this->canShow('up_work_minutes')
            || $this->canShow('up_timezone')
            || $this->canShow('up_base_amount')
            || $this->canShow('up_commission_percent')
            || $this->canShow('up_bonus_active')
            || $this->canShow('up_leaderboard_enabled')
            || $this->canShow('up_inbox_show_limit_leads')
            || $this->canShow('up_default_take_limit_leads')
            || $this->canShow('up_min_percent_for_take_leads')
            || $this->canShow('up_frequency_minutes')
            || $this->canShow('up_call_expert_limit')
            || $this->canShow('up_call_user_level');
    }

    public function canShowProfileSettings(): bool
    {
        return true;
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
        return true;
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
        return true;
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
