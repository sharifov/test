<?php

namespace common\models\search;

use common\models\UserGroupAssign;
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
            [['upp_email', 'upp_phone_number', 'upp_tw_phone_number', 'upp_tw_sip_id', 'upp_created_dt', 'upp_updated_dt'], 'safe'],
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
        $query = UserProjectParams::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['upp_updated_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
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
            'upp_user_id' => $this->upp_user_id,
            'upp_project_id' => $this->upp_project_id,
            'upp_created_dt' => $this->upp_created_dt ? date('Y-m-d', strtotime($this->upp_created_dt)) : null,
            'upp_updated_dt' => $this->upp_updated_dt ? date('Y-m-d', strtotime($this->upp_updated_dt)) : null,
            'upp_updated_user_id' => $this->upp_updated_user_id,
        ]);

        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        $query->andFilterWhere(['like', 'upp_email', $this->upp_email])
            ->andFilterWhere(['like', 'upp_phone_number', $this->upp_phone_number])
            ->andFilterWhere(['like', 'upp_tw_phone_number', $this->upp_tw_phone_number])
            ->andFilterWhere(['like', 'upp_tw_sip_id', $this->upp_tw_sip_id]);

        return $dataProvider;
    }
}
