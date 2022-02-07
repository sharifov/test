<?php

namespace src\model\emailList\services;

use common\models\Department;
use common\models\DepartmentEmailProject;
use common\models\DepartmentEmailProjectQuery;
use common\models\Project;
use common\models\query\UserProjectParamsQuery;
use common\models\UserProjectParams;
use src\model\emailList\entity\EmailList;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class AvailableEmailList
 *
 * @property array|null $list
 * @property int $userId
 * @property int $projectId
 * @property int|null $departmentId
 * @property bool $isGeneralFirst
 */
class AvailableEmailList
{
    private const SORT_GENERAL_FIRST = SORT_DESC;
    private const SORT_PERSONAL_FIRST = SORT_ASC;

    /** @var AvailableEmail[]|null */
    private ?array $list = null;

    private int $userId;
    private int $projectId;
    private ?int $departmentId;
    private bool $isGeneralFirst;

    public function __construct(int $userId, int $projectId, ?int $departmentId, bool $isGeneralFirst)
    {
        $this->userId = $userId;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->isGeneralFirst = $isGeneralFirst;
    }

    public function getFormattedList(): array
    {
        $list = [];
        foreach ($this->getList() as $email) {
            $list[$email->email] = $email->format();
        }
        return $list;
    }

    /**
     * @return AvailableEmail[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        if ($this->isGeneralFirst) {
            $sort = self::SORT_GENERAL_FIRST;
        } else {
            $sort = self::SORT_PERSONAL_FIRST;
        }

        $emails = (new Query())
            ->select([
                'project_id',
                'email_list_id',
                'email',
                'type_id',
                'type',
                'project.name as project',
                'department_id',
                'dep_name as department'
            ])
            ->from(self::getUserEmails($this->userId, $this->projectId)->union(self::getDepartmentEmails($this->projectId, $this->departmentId)))
            ->innerJoin(['project' => Project::tableName()], 'project.id = project_id')
            ->leftJoin(Department::tableName(), 'dep_id = department_id')
            ->orderBy(['type_id' => $sort])
            ->all();

        foreach ($emails as $email) {
            $this->list[] = AvailableEmail::createFromRow($email);
        }

        return $this->list;
    }

    public function isExist(string $value): bool
    {
        foreach ($this->getList() as $email) {
            if ($email->isEqual($value)) {
                return true;
            }
        }
        return false;
    }

    private static function getDepartmentEmails(int $projectId, ?int $departmentId): DepartmentEmailProjectQuery
    {
        $query = DepartmentEmailProject::find()
            ->select([
                'dep_project_id as project_id',
                'dep_email_list_id as email_list_id',
                'el_email as email',
                'dep_dep_id as department_id'
            ])
            ->addSelect(new Expression(AvailableEmail::GENERAL_ID . ' as type_id, "' . AvailableEmail::GENERAL . '" as type'))
            ->innerJoin(EmailList::tableName(), 'el_id = dep_email_list_id')
            ->andWhere([
                'dep_enable' => true,
                'el_enabled' => true,
                'dep_project_id' => $projectId,
                'dep_default' => true,
            ]);

        if ($departmentId) {
            $query->andWhere([
                'OR',
                ['dep_dep_id' => $departmentId],
                ['IS', 'dep_dep_id', null],
            ]);
        } else {
            $query->andWhere(['IS', 'dep_dep_id', null]);
        }

        return $query;
    }

    private static function getUserEmails(int $userId, int $projectId): UserProjectParamsQuery
    {
        return UserProjectParams::find()
            ->select([
                'upp_project_id as project_id',
                'upp_email_list_id as email_list_id',
                'el_email as email',
                'upp_dep_id as department_id'
            ])
            ->addSelect(new Expression(AvailableEmail::PERSONAL_ID . ' as type_id, "' . AvailableEmail::PERSONAL . '" as type'))
            ->innerJoin(EmailList::tableName(), 'el_id = upp_email_list_id')
            ->andWhere([
                'el_enabled' => true,
                'upp_user_id' => $userId,
                'upp_project_id' => $projectId,
            ]);
    }
}
