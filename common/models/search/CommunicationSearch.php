<?php

namespace common\models\search;

use common\models\UserGroupAssign;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Employee;
use yii\db\Expression;

/**
 * CommunicationSearch represents the model behind the search form
 */
class CommunicationSearch extends Model
{
    public const COMM_TYPE_SMS      = 1;
    public const COMM_TYPE_VOICE    = 2;
    public const COMM_TYPE_EMAIL    = 3;

    public const COMM_TYPE_LIST    = [
        self::COMM_TYPE_SMS => 'SMS',
        self::COMM_TYPE_VOICE => 'Voice',
        self::COMM_TYPE_EMAIL => 'Email',
    ];

    public $id;
    public $project_id;
    public $created_dt;
    public $lead_id;
    public $communication_type_id;
    public $created_user_id;
    public $supervision_id;
    public $user_group_id;

    public $agent_phone;
    public $client_phone;

    public $datetime_start;
    public $datetime_end;
    public $date_range;

    //public $online;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'lead_id', 'communication_type_id', 'created_user_id', 'supervision_id', 'user_group_id'], 'integer'],
            [['created_dt'], 'date', 'format' => 'php:Y-m-d'],
            [['datetime_start', 'datetime_end', 'agent_phone', 'client_phone'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function search($params)
    {
        $query1 = (new \yii\db\Query())
            ->select(['id' => 's_id', 'communication_type_id' => new Expression('"1"'), 'lead_id' => 's_lead_id', 'created_dt' => 's_created_dt', 'project_id' => 's_project_id', 'created_user_id' => 's_created_user_id'])
            ->from('sms')
            ->orderBy(['s_id' => SORT_DESC]);
        //->where(['s_lead_id' => $lead->id]);


        $query2 = (new \yii\db\Query())
            ->select(['id' => 'c_id', 'communication_type_id' => new Expression('"2"'), 'lead_id' => 'c_lead_id', 'created_dt' => 'c_created_dt', 'project_id' => 'c_project_id', 'created_user_id' => 'c_created_user_id'])
            ->from('call')
            ->orderBy(['c_id' => SORT_DESC]);
        //->where(['c_lead_id' => $lead->id]);

        /*$query3 = (new \yii\db\Query())
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_lead_id AS lead_id', 'e_created_dt AS created_dt'])
            ->from('email')
            ->orderBy(['e_id' => SORT_DESC]);*/
        //->where(['e_lead_id' => $lead->id]);

        $query = (new \yii\db\Query())
            ->from(['union_table' => $query1->union($query2)]) //->union($query3)
        ; //->orderBy(['created_dt' => SORT_DESC, 'id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_dt' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => [

                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                        //'default' => SORT_DESC,
                        'label' => 'Id',
                    ],

                    /*'user_group_id' => [
                        'asc' => ['user_group_id' => SORT_ASC],
                        'desc' => ['user_group_id' => SORT_DESC],
                        //'default' => SORT_DESC,
                        'label' => 'user group',
                    ],*/

                    'created_user_id' => [
                        'asc' => ['created_user_id' => SORT_ASC],
                        'desc' => ['created_user_id' => SORT_DESC],
                        //'default' => SORT_DESC,
                        'label' => 'Created User',
                    ],

                    'project_id' => [
                        'asc' => ['project_id' => SORT_ASC],
                        'desc' => ['project_id' => SORT_DESC],
                        //'default' => SORT_DESC,
                        'label' => 'Project',
                    ],

                    'communication_type_id' => [
                        'asc' => ['communication_type_id' => SORT_ASC],
                        'desc' => ['communication_type_id' => SORT_DESC],
                        //'default' => SORT_DESC,
                        'label' => 'Communication type',
                    ],

                    'lead_id' => [
                        'asc' => ['lead_id' => SORT_ASC],
                        'desc' => ['lead_id' => SORT_DESC],
                        //'default' => SORT_DESC,
                        'label' => 'Lead Id',
                    ],
                    'created_dt' => [
                        'asc' => ['created_dt' => SORT_ASC],
                        'desc' => ['created_dt' => SORT_DESC],
                        //'default' => SORT_DESC,
                        'label' => 'Created Date',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'lead_id'   => $this->lead_id,
            'communication_type_id' => $this->communication_type_id,
            'created_user_id'   => $this->created_user_id,
            //'user_group_id' => $this->user_group_id,
        ]);

        if (empty($this->created_dt) && isset($params['CommunicationSearch']['date_range'])) {
            $query->andFilterWhere(['>=', 'created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
                ->andFilterWhere(['<=', 'created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        } elseif (!empty($this->created_dt)) {
            //$query->andFilterWhere(['=','DATE(created_dt)', $this->created_dt]);
            $query->andFilterWhere(['>=', 'created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->created_dt))])
                ->andFilterWhere(['<=', 'created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->created_dt) + 3600 * 24)]);
        }

        if ($this->user_group_id > 0) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['=', 'ugs_group_id', $this->user_group_id]);
            $query->andWhere(['IN', 'created_user_id', $subQuery]);
        }

        /*if ($this->user_params_project_id > 0) {
            $subQuery = UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])->where(['=', 'upp_project_id', $this->user_params_project_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->user_call_type_id > 0) {
            $subQuery = UserProfile::find()->select(['DISTINCT(up_user_id)'])->where(['=', 'up_call_type_id', $this->user_call_type_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->user_project_id > 0) {
            $subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['=', 'project_id', $this->user_project_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->online > 0) {
            if ($this->online == 1) {
                $subQuery = UserConnection::find()->select(['DISTINCT(uc_user_id)']);
                $query->andWhere(['IN', 'employees.id', $subQuery]);
            } elseif ($this->online == 2) {
                $subQuery = UserConnection::find()->select(['DISTINCT(uc_user_id)']);
                $query->andWhere(['NOT IN', 'employees.id', $subQuery]);
            }
        }
*/
        // $query->andFilterWhere(['like', 'username', $this->username]);
        /*            ->andFilterWhere(['like', 'full_name', $this->full_name])
                    ->andFilterWhere(['like', 'email', $this->email]);*/


        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'created_user_id', $subQuery]);
        }
        return $dataProvider;
    }
}
