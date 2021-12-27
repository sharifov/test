<?php

namespace frontend\widgets\newWebPhone;

use common\models\DepartmentPhoneProject;
use common\models\DepartmentPhoneProjectUserGroup;
use common\models\ProjectEmployeeAccess;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use common\models\UserProjectParams;
use sales\helpers\setting\SettingHelper;
use sales\model\phoneList\entity\PhoneList;
use yii\rbac\ManagerInterface;

/**
 * Class AvailablePhones
 *
 * @property int $userId
 * @property ManagerInterface $authManager
 * @property AvailablePhone[]|null $phones
 */
class AvailablePhones
{
    private int $userId;
    private ManagerInterface $authManager;
    private ?array $phones = null;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->authManager = \Yii::$app->authManager;
    }

    public function getPhone(string $number): ?AvailablePhone
    {
        foreach ($this->getPhones() as $phone) {
            if ($phone->number === $number) {
                return $phone;
            }
        }
        return null;
    }

    public function formatPhonesForSelectList(): array
    {
        $phones = $this->getPhones();

        if (!$phones) {
            return [
                'selected' => [
                    'value' => '',
                    'project' => 'no number',
                    'projectId' => '',
                ],
                'options' => [],
            ];
        }

        $result = [
            'selected' => [],
            'options' => [],
        ];

        foreach ($phones as $phone) {
            $result['options'][] = [
                'value' => $phone->number,
                'project' => $phone->title,
                'projectId' => $phone->projectId,
            ];
        }

        $result['selected']['value'] = $phones[0]->number;
        $result['selected']['project'] = $phones[0]->title;
        $result['selected']['projectId'] = $phones[0]->projectId;

        return $result;
    }

    /**
     * @return AvailablePhone[]
     */
    private function getPhones(): array
    {
        if ($this->phones !== null) {
            return $this->phones;
        }

        if (!$this->authManager->checkAccess($this->userId, 'PhoneWidget_Dialpad')) {
            $this->phones = [];
            return $this->phones;
        }

        $userPhones = self::getUserProjectParams($this->userId);

        if (!SettingHelper::isAllowToUseGeneralLinePhones()) {
            $this->phones = self::createPhonesFromArray($userPhones);
            return $this->phones;
        }

        $departmentPhones = self::getDepartmentPhones($this->userId);

        $phones = array_merge($userPhones, $departmentPhones);
        $unique_array = [];
        foreach ($phones as $phone) {
            $hash = $phone['phone_number'];
            $unique_array[$hash] = $phone;
        }
        $this->phones = self::createPhonesFromArray(array_values($unique_array));
        return $this->phones;
    }

    private static function getUserProjectParams(int $userId): array
    {
        return UserProjectParams::find()
            ->select(['upp_project_id as project_id', 'pl_phone_number as phone_number', 'p.name as title', 'upp_dep_id as department_id'])
            ->byUserId($userId)
            ->withExistingPhoneInPhoneList()
            ->withProject()
            ->asArray()
            ->all();
    }

    private static function getDepartmentPhones(int $userId): array
    {
        return DepartmentPhoneProject::find()
            ->select(['min(dpp_project_id) as project_id', 'pl_phone_number as phone_number', 'min(pl_title) as title', 'min(dpp_dep_id) as department_id'])
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
            ->groupBy(['pl_phone_number', 'dpp_dep_id'])
            ->asArray()
            ->all();
    }

    /**
     * @param array $data
     * @return AvailablePhone[]
     */
    private static function createPhonesFromArray(array $data): array
    {
        $phones = [];
        foreach ($data as $phone) {
            $phones[] = new AvailablePhone(
                $phone['phone_number'],
                (int)$phone['project_id'],
                $phone['department_id'] ? (int)$phone['department_id'] : null,
                $phone['title']
            );
        }
        return $phones;
    }
}
