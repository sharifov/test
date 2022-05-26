<?php

namespace common\models\search;

use common\models\Employee;
use common\models\UserGroupAssign;
use src\model\userVoiceMail\entity\UserVoiceMail;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserProjectParams;

/**
 * UserProjectParamsSearch represents the model behind the search form of `common\models\UserProjectParams`.
 */
class UserProjectParamsSearch extends UserProjectParams
{
    public $supervision_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['upp_user_id', 'upp_project_id', 'upp_updated_user_id', 'supervision_id'], 'integer'],
//            ['upp_email', 'safe'],
//            ['upp_tw_phone_number', 'safe'],
            [['upp_phone_number', 'upp_tw_sip_id', 'upp_allow_general_line', 'upp_allow_transfer'], 'safe'],
            ['upp_phone_list_id', 'integer'],
            ['upp_email_list_id', 'integer'],
            ['upp_dep_id', 'integer'],

            ['upp_vm_user_status_id', 'integer'],
            ['upp_vm_user_status_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['upp_vm_enabled', 'boolean'],
            ['upp_vm_id', 'string'],

            [['upp_created_dt', 'upp_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
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
     */
    public function search($params)
    {
        $query = UserProjectParams::find()->with('uppUpdatedUser', 'uppUser', 'uppProject', 'emailList', 'phoneList', 'uppDep')->joinWith(['voiceMail']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['upp_updated_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->sort->attributes['upp_vm_id'] = [
            'asc' => [UserVoiceMail::tableName() . '.uvm_name' => SORT_ASC],
            'desc' => [UserVoiceMail::tableName() . '.uvm_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->upp_updated_dt) {
            $query->andFilterWhere(['>=', 'upp_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->upp_updated_dt))])
                ->andFilterWhere(['<=', 'upp_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->upp_updated_dt) + 3600 * 24)]);
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'upp_user_id' => $this->upp_user_id,
            'upp_project_id' => $this->upp_project_id,
            'upp_created_dt' => $this->upp_created_dt ? date('Y-m-d', strtotime($this->upp_created_dt)) : null,
            //'upp_updated_dt' => $this->upp_updated_dt ? date('Y-m-d', strtotime($this->upp_updated_dt)) : null,
            'upp_updated_user_id' => $this->upp_updated_user_id,
            'upp_allow_general_line' => $this->upp_allow_general_line,
            'upp_allow_transfer' => $this->upp_allow_transfer,
            'upp_email_list_id' => $this->upp_email_list_id,
            'upp_phone_list_id' => $this->upp_phone_list_id,
            'upp_dep_id' => $this->upp_dep_id,
            'upp_vm_enabled' => $this->upp_vm_enabled,
        ]);

        if ($this->upp_vm_user_status_id) {
            if ($this->upp_vm_user_status_id === UserProjectParams::VM_USER_STATUS_ALL) {
                $query->andWhere([
                    'OR',
                    ['IS', 'upp_vm_user_status_id', null],
                    ['upp_vm_user_status_id' => UserProjectParams::VM_USER_STATUS_ALL],
                ]);
            } else {
                $query->andWhere(['upp_vm_user_status_id' => $this->upp_vm_user_status_id]);
            }
        }

        if ($this->upp_vm_id) {
            $query->andWhere(['like', UserVoiceMail::tableName() . '.uvm_name', $this->upp_vm_id]);
        }

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

//        $query
//            ->andFilterWhere(['like', 'upp_email', $this->upp_email])
//            ->andFilterWhere(['like', 'upp_tw_phone_number', $this->upp_tw_phone_number]);

        return $dataProvider;
    }
}
