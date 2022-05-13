<?php

namespace src\model\phoneList\entity\search;

use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\UserProjectParams;
use src\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use src\model\phoneList\entity\PhoneList;

/**
 *  @property string|null $used_for
 *  @property int|null $projects
 *  @property array|null $user_project_params_users
 */
class PhoneListSearch extends PhoneList
{
    public const USED_FOR_GENERAL_AND_PERSONAL = 'general_and_personal';
    public const USED_FOR_GENERAL = 'general';
    public const USED_FOR_PERSONAL = 'personal';
    public const USED_FOR_NONE = 'unused';

    private const USED_FOR_LIST = [
        self::USED_FOR_GENERAL_AND_PERSONAL => 'General and Personal',
        self::USED_FOR_GENERAL => 'General',
        self::USED_FOR_PERSONAL => 'Personal',
        self::USED_FOR_NONE => 'Unused',
    ];

    public $used_for;

    public $projects;

    public $user_project_params_users;

    public static function getUsedForList(): array
    {
        return self::USED_FOR_LIST;
    }

    public function rules(): array
    {
        return [
            [['pl_id', 'pl_created_user_id', 'pl_updated_user_id'], 'integer'],
            ['pl_enabled', 'boolean'],
            ['pl_title', 'string'],
            ['pl_phone_number', 'string'],
            ['used_for', 'string'],
            ['projects', 'integer'],
            ['user_project_params_users', 'integer'],
            [['pl_created_dt', 'pl_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            [['used_for'], 'in', 'range' => array_keys(self::getUsedForList())],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = PhoneList::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->pl_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pl_created_dt', $this->pl_created_dt, $user->timezone);
        }

        if ($this->pl_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pl_updated_dt', $this->pl_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pl_id' => $this->pl_id,
            'pl_enabled' => $this->pl_enabled,
            'pl_created_user_id' => $this->pl_created_user_id,
            'pl_updated_user_id' => $this->pl_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pl_phone_number', $this->pl_phone_number])
            ->andFilterWhere(['like', 'pl_title', $this->pl_title]);

        if ($this->projects) {
            $userProjectEntries = UserProjectParams::find()
                ->select(['upp_phone_list_id'])
                ->where(['IN','upp_project_id',  $this->projects])
                ->column();
            $departmentProjectEntries = DepartmentPhoneProject::find()
                ->select(['dpp_phone_list_id'])
                ->where(['IN', 'dpp_project_id', $this->projects])
                ->column();
            $phoneListIds =  array_merge($userProjectEntries, $departmentProjectEntries);
            $query->andWhere([
                'IN',
                'pl_id',
                $phoneListIds
            ]);
        }
        if ($this->user_project_params_users) {
            $phoneListIds = UserProjectParams::find()
                ->select(['upp_phone_list_id'])
                ->where([ 'IN', 'upp_user_id',  $this->user_project_params_users])
                ->column();
            $query->andWhere([
                'IN',
                'pl_id',
                $phoneListIds
            ]);
        }

        if ($this->used_for) {
            if ($this->used_for === self::USED_FOR_GENERAL) {
                $query->innerJoinWith(['departmentPhoneProject']);
            } elseif ($this->used_for === self::USED_FOR_PERSONAL) {
                $query->innerJoinWith(['userProjectParams']);
            } elseif ($this->used_for === self::USED_FOR_NONE) {
                $uppPhoneListQuery = UserProjectParams
                    ::find()
                    ->select(['upp_phone_list_id'])
                    ->where(['NOT', ['upp_phone_list_id' => 'NULL']]);
                $dppPhoneListQuery = DepartmentPhoneProject
                    ::find()
                    ->select(['dpp_phone_list_id'])
                    ->where(['NOT', ['dpp_phone_list_id' => 'NULL']]);
                $query->andWhere(['NOT', ['pl_id' => $uppPhoneListQuery]])
                     ->andWhere(['NOT', ['pl_id' => $dppPhoneListQuery]]);
            } elseif ($this->used_for === self::USED_FOR_GENERAL_AND_PERSONAL) {
                $query->innerJoinWith(['departmentPhoneProject'])
                    ->innerJoinWith(['userProjectParams']);
            }
        } else {
            $query->joinWith(['departmentPhoneProject', 'userProjectParams']);
        }

        return $dataProvider;
    }
}
