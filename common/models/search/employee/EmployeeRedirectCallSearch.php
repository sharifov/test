<?php

namespace common\models\search\employee;

use common\components\validators\IsArrayValidator;
use common\models\Call;
use common\models\Employee;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class EmployeeRedirectCallSearch extends Model
{
    public $username;
    public $roles;

    private bool $_filtered = false;

    public function rules(): array
    {
        return [
            ['username', 'string'],
            ['roles', IsArrayValidator::class]
        ];
    }

    public function search(Call $call, int $userId, $queryParams): ArrayDataProvider
    {
        if ($this->load($queryParams) && $this->validate()) {
            $this->_filtered = true;
        }

        return new ArrayDataProvider([
            'allModels' => $this->getUsers($call, $userId),
            'pagination' => false
        ]);
    }

    /**
     * @param Call $call
     * @param int $userId
     * @return array
     */
    private function getUsers(Call $call, int $userId): array
    {
        $userList = Employee::getUsersForRedirectCall($call);
        $users = [];

        $query = Employee::find()->andWhere(['id' => ArrayHelper::getColumn($userList, 'tbl_user_id')]);

        if ($this->_filtered) {
            $query->andFilterWhere(['like', 'username', $this->username]);

            if ($this->roles) {
                $query->andWhere(['IN', 'employees.id', array_keys(Employee::getListByRole($this->roles))]);
            }
        }

        $userModels = $query->indexBy('id')->all();

        if ($userList) {
            foreach ($userList as $userItem) {
                $agentId = (int)$userItem['tbl_user_id'];
                if ($agentId === $userId) {
                    continue;
                }
                /** @var ?Employee $userModel */
                $userModel = $userModels[(int)$userItem['tbl_user_id']] ?? null;

                if ($userModel && ($userModel->isAgent() || $userModel->isSupAgent() || $userModel->isExAgent() || $userModel->isSupervision() || $userModel->isSupSuper() || $userModel->isExSuper())) {
                    $users[] = [
                        'model' => $userModel,
                        'isBusy' => (int)$userItem['tbl_has_lead_redial_access'] > 0,
                        'departments' => $userModel->getUserDepartments()->select('ud_dep_id')->indexBy('ud_dep_id')->column()
                    ];
                }
            }
        }
        return $users;
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }
}
