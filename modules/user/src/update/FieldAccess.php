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
 */
class FieldAccess
{
    private Employee $user;
    private bool $isNewRecord;

    public function __construct(Employee $user, bool $isNewRecord)
    {
        $this->user = $user;
        $this->isNewRecord = $isNewRecord;
    }

    public function canViewStatus(): bool
    {
        return $this->canView('status');
    }

    public function canEditStatus(): bool
    {
        return $this->canEdit('status');
    }

    public function canViewNickname(): bool
    {
        return $this->canView('nickname');
    }

    public function canEditNickname(): bool
    {
        return $this->canEdit('nickname');
    }

    public function canViewPassword(): bool
    {
        return $this->canView('password');
    }

    public function canEditPassword(): bool
    {
        return $this->canEdit('password');
    }

    public function canViewFullName(): bool
    {
        return $this->canView('full_name');
    }

    public function canEditFullName(): bool
    {
        return $this->canEdit('full_name');
    }

    public function canViewEmail(): bool
    {
        return $this->canView('email');
    }

    public function canEditEmail(): bool
    {
        return $this->canEdit('email');
    }

    public function canViewUsername(): bool
    {
        return $this->canView('username');
    }

    public function canEditUsername(): bool
    {
        return $this->canEdit('username');
    }

    public function canViewAclRulesActivated(): bool
    {
        return $this->canView('acl_rules_activated');
    }

    public function canEditAclRulesActivated(): bool
    {
        return $this->canEdit('acl_rules_activated');
    }

    public function canViewRoles(): bool
    {
        return $this->canView('form_roles');
    }

    public function canEditRoles(): bool
    {
        return $this->canEdit('form_roles');
    }

    public function canViewUserGroups(): bool
    {
        return $this->canView('user_groups');
    }

    public function canEditUserGroups(): bool
    {
        return $this->canEdit('user_groups');
    }

    public function canViewProjects(): bool
    {
        return $this->canView('user_projects');
    }

    public function canEditProjects(): bool
    {
        return $this->canEdit('user_projects');
    }

    public function canViewDepartments(): bool
    {
        return $this->canView('user_departments');
    }

    public function canEditDepartments(): bool
    {
        return $this->canEdit('user_departments');
    }

    public function canViewClientChatUserChannels(): bool
    {
        return $this->canView('client_chat_user_channel');
    }

    public function canEditClientChatUserChannels(): bool
    {
        return $this->canEdit('client_chat_user_channel');
    }

    public function canViewUserShiftAssign(): bool
    {
        return $this->canView('user_shift_assigns');
    }

    public function canEditUserShiftAssign(): bool
    {
        return $this->canEdit('user_shift_assigns');
    }

    public function canViewWorkStartTime(): bool
    {
        return $this->canView('up_work_start_tm');
    }

    public function canEditWorkStartTime(): bool
    {
        return $this->canEdit('up_work_start_tm');
    }

    public function canViewWorkMinutes(): bool
    {
        return $this->canView('up_work_minutes');
    }

    public function canEditWorkMinutes(): bool
    {
        return $this->canEdit('up_work_minutes');
    }

    public function canViewTimeZone(): bool
    {
        return $this->canView('up_timezone');
    }

    public function canEditTimeZone(): bool
    {
        return $this->canEdit('up_timezone');
    }

    public function canViewBaseAmount(): bool
    {
        return $this->canView('up_base_amount');
    }

    public function canEditBaseAmount(): bool
    {
        return $this->canEdit('up_base_amount');
    }

    public function canViewCommissionPercent(): bool
    {
        return $this->canView('up_commission_percent');
    }

    public function canEditCommissionPercent(): bool
    {
        return $this->canEdit('up_commission_percent');
    }

    public function canViewBonusActive(): bool
    {
        return $this->canView('up_bonus_active');
    }

    public function canEditBonusActive(): bool
    {
        return $this->canEdit('up_bonus_active');
    }

    public function canViewLeaderboardEnabled(): bool
    {
        return $this->canView('up_leaderboard_enabled');
    }

    public function canEditLeaderboardEnabled(): bool
    {
        return $this->canEdit('up_leaderboard_enabled');
    }

    public function canViewInboxShowLimitLeads(): bool
    {
        return $this->canView('up_inbox_show_limit_leads');
    }

    public function canEditInboxShowLimitLeads(): bool
    {
        return $this->canEdit('up_inbox_show_limit_leads');
    }

    public function canViewDefaultTakeLimitLeads(): bool
    {
        return $this->canView('up_default_take_limit_leads');
    }

    public function canEditDefaultTakeLimitLeads(): bool
    {
        return $this->canEdit('up_default_take_limit_leads');
    }

    public function canViewMinPercentForTakeLeads(): bool
    {
        return $this->canView('up_min_percent_for_take_leads');
    }

    public function canEditMinPercentForTakeLeads(): bool
    {
        return $this->canEdit('up_min_percent_for_take_leads');
    }

    public function canViewFrequencyMinutes(): bool
    {
        return $this->canView('up_frequency_minutes');
    }

    public function canEditFrequencyMinutes(): bool
    {
        return $this->canEdit('up_frequency_minutes');
    }

    public function canViewCallExpertLimit(): bool
    {
        return $this->canView('up_call_expert_limit');
    }

    public function canEditCallExpertLimit(): bool
    {
        return $this->canEdit('up_call_expert_limit');
    }

    public function canViewCallUserLevel(): bool
    {
        return $this->canView('up_call_user_level');
    }

    public function canEditCallUserLevel(): bool
    {
        return $this->canEdit('up_call_user_level');
    }

    public function canViewJoinDate(): bool
    {
        return $this->canView('up_join_date');
    }

    public function canEditJoinDate(): bool
    {
        return $this->canEdit('up_join_date');
    }

    public function canViewSkill(): bool
    {
        return $this->canView('up_skill');
    }

    public function canEditSkill(): bool
    {
        return $this->canEdit('up_skill');
    }

    public function canViewCallTypeId(): bool
    {
        return $this->canView('up_call_type_id');
    }

    public function canEditCallTypeId(): bool
    {
        return $this->canEdit('up_call_type_id');
    }

    public function canView2faSecret(): bool
    {
        return $this->canView('up_2fa_secret');
    }

    public function canEdit2faSecret(): bool
    {
        return $this->canEdit('up_2fa_secret');
    }

    public function canView2faEnable(): bool
    {
        return $this->canView('up_2fa_enable');
    }

    public function canEdit2faEnable(): bool
    {
        return $this->canEdit('up_2fa_enable');
    }

    public function canViewTelegram(): bool
    {
        return $this->canView('up_telegram');
    }

    public function canEditTelegram(): bool
    {
        return $this->canEdit('up_telegram');
    }

    public function canViewTelegramEnable(): bool
    {
        return $this->canView('up_telegram_enable');
    }

    public function canEditTelegramEnable(): bool
    {
        return $this->canEdit('up_telegram_enable');
    }

    public function canViewAutoRedial(): bool
    {
        return $this->canView('up_auto_redial');
    }

    public function canEditAutoRedial(): bool
    {
        return $this->canEdit('up_auto_redial');
    }

    public function canViewKpiEnable(): bool
    {
        return $this->canView('up_kpi_enable');
    }

    public function canEditKpiEnable(): bool
    {
        return $this->canEdit('up_kpi_enable');
    }

    public function canViewShowInContactList(): bool
    {
        return $this->canView('up_show_in_contact_list');
    }

    public function canEditShowInContactList(): bool
    {
        return $this->canEdit('up_show_in_contact_list');
    }

    public function canViewCallRecordingDisabled(): bool
    {
        return $this->canView('up_call_recording_disabled');
    }

    public function canEditCallRecordingDisabled(): bool
    {
        return $this->canEdit('up_call_recording_disabled');
    }

    private function canView(?string $field): bool
    {
        $userAbacDto = new UserAbacDto($field);
        $userAbacDto->isNewRecord = $this->isNewRecord;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, User field view*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_VIEW, $this->user);
    }

    private function canEdit(?string $field): bool
    {
        $userAbacDto = new UserAbacDto($field);
        $userAbacDto->isNewRecord = $this->isNewRecord;
        /** @abac new $userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, User field edit*/
        return \Yii::$app->abac->can($userAbacDto, UserAbacObject::USER_FORM, UserAbacObject::ACTION_EDIT, $this->user);
    }
}
