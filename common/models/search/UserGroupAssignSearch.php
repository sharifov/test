<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserGroupAssign;

/**
 * UserGroupAssignSearch represents the model behind the search form of `common\models\UserGroupAssign`.
 */
class UserGroupAssignSearch extends UserGroupAssign
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ugs_user_id', 'ugs_group_id'], 'integer'],
            [['ugs_updated_dt'], 'safe'],
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
        $query = UserGroupAssign::find()->with('ugsGroup');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ugs_updated_dt){
            $query->andFilterWhere(['>=', 'ugs_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ugs_updated_dt))])
                ->andFilterWhere(['<=', 'ugs_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ugs_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ugs_user_id' => $this->ugs_user_id,
            'ugs_group_id' => $this->ugs_group_id,
        ]);

        return $dataProvider;
    }
}
