<?php

namespace frontend\widgets;

use common\models\Call;
use common\models\DepartmentEmailProjectUserGroup;
use common\models\DepartmentPhoneProject;
use common\models\DepartmentPhoneProjectUserGroup;
use common\models\Employee;
use common\models\ProjectEmployeeAccess;
use common\models\UserCallStatus;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use sales\model\phoneList\entity\PhoneList;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class NewWebPhoneWidget
 *
 * @property int $userId
 */
class NewWebPhoneWidget extends Widget
{
    public $userId;

    public function run(): string
    {
        $userProfile = UserProfile::find()->where(['up_user_id' => $this->userId])->limit(1)->one();
        if (!$userProfile || (int) $userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB) {
            return '';
        }

        $userPhoneProject = $this->getAvailablePhones($this->userId);

        return $this->render('web_phone_new', [
            'formattedPhoneProject' => json_encode($this->formatDataForSelectList($userPhoneProject)),
            'userPhones' => array_keys($this->getUserPhones()),
            'userEmails' => array_keys($this->getUserEmails()),
            'userCallStatus' => UserCallStatus::find()->where(['us_user_id' => $this->userId])->orderBy(['us_id' => SORT_DESC])->limit(1)->one(),
            'countMissedCalls' => Call::find()->byCreatedUser($this->userId)->missed()->count(),
        ]);
    }

    private function getUserPhones(): array
    {
        return Employee::getPhoneList($this->userId);
    }

    private function getUserEmails(): array
    {
        return Employee::getEmailList($this->userId);
    }

    private function getAvailablePhones(int $userId): array
    {
        $userPhones = $this->getUserProjectParams($userId);
        if (!SettingHelper::isAllowToUseGeneralLinePhones()) {
            return $userPhones;
        }
        $departmentPhones = $this->getDepartmentPhones($userId);
        $phones = array_merge($userPhones, $departmentPhones);
        $unique_array = [];
        foreach ($phones as $phone) {
            $hash = $phone['phone_number'];
            $unique_array[$hash] = $phone;
        }
        return array_values($unique_array);
    }

    private function getUserProjectParams(int $userId): array
    {
        return UserProjectParams::find()
            ->select(['upp_project_id as project_id', 'pl_phone_number as phone_number', 'p.name as title'])
            ->byUserId($userId)
            ->withExistingPhoneInPhoneList()
            ->withProject()
            ->asArray()
            ->all();
    }

    private function getDepartmentPhones(int $userId): array
    {
        return DepartmentPhoneProject::find()
            ->select(['min(dpp_project_id) as project_id', 'pl_phone_number as phone_number', 'min(pl_title) as title'])
            ->leftJoin(PhoneList::tableName(), 'pl_id = dpp_phone_list_id and pl_enabled = :pl_enabled', ['pl_enabled' => true])
            ->leftJoin(DepartmentPhoneProjectUserGroup::tableName(), 'dug_dpp_id = dpp_id')
            ->andWhere([
                'dpp_project_id' => ProjectEmployeeAccess::find()->select(['project_id'])->andWhere(['employee_id' => $userId])
            ])
            ->andWhere([
                'OR',
                ['IS', 'dpp_dep_id', null],
                ['dpp_dep_id' => UserDepartment::find()->select(['ud_dep_id'])->andWhere(['ud_user_id' => $userId])],
            ])
            ->andWhere([
                'OR',
                ['IS', 'dug_ug_id', null],
                ['dug_ug_id' => UserGroupAssign::find()->select(['ugs_group_id'])->andWhere(['ugs_user_id' => $userId])]
            ])
            ->groupBy('pl_phone_number')
            ->asArray()
            ->all();
    }

    private function formatDataForSelectList(array $userProjectPhones): array
    {
        $result = [
            'selected' => [],
            'options' => []
        ];

        if (!Auth::can('PhoneWidget_Dialpad')) {
            return $result;
        }

        foreach ($userProjectPhones as $phone) {
            $result['options'][] = [
                'value' => $phone['phone_number'],
                'project' => $phone['title'],
                'projectId' => $phone['project_id']
            ];
        }

        if (!isset($userProjectPhones[0])) {
            $result['selected']['value'] = '';
            $result['selected']['project'] = 'no number';
            $result['selected']['projectId'] = '';
        } else {
            $result['selected']['value'] = $userProjectPhones[0]['phone_number'];
            $result['selected']['project'] = $userProjectPhones[0]['title'];
            $result['selected']['projectId'] = $userProjectPhones[0]['project_id'];
        }

        return $result;
    }
}
