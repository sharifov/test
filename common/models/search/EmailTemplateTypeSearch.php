<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EmailTemplateType;

/**
 * EmailTemplateTypeSearch represents the model behind the search form of `common\models\EmailTemplateType`.
 */
class EmailTemplateTypeSearch extends EmailTemplateType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['etp_id', 'etp_created_user_id', 'etp_updated_user_id', 'etp_dep_id'], 'integer'],
            [['etp_key', 'etp_name', 'etp_origin_name', 'etp_hidden', 'etp_created_dt', 'etp_updated_dt'], 'safe'],
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
        $query = EmailTemplateType::find()->with('etpCreatedUser', 'etpUpdatedUser');

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

        if ($this->etp_created_dt) {
            $query->andFilterWhere(['>=', 'etp_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->etp_created_dt))])
                ->andFilterWhere(['<=', 'etp_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->etp_created_dt) + 3600 * 24)]);
        }

        if ($this->etp_updated_dt) {
            $query->andFilterWhere(['>=', 'etp_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->etp_updated_dt))])
                ->andFilterWhere(['<=', 'etp_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->etp_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'etp_id' => $this->etp_id,
            'etp_hidden' => $this->etp_hidden,
            'etp_created_user_id' => $this->etp_created_user_id,
            'etp_updated_user_id' => $this->etp_updated_user_id,
            'etp_dep_id'    => $this->etp_dep_id
        ]);

        $query->andFilterWhere(['like', 'etp_key', $this->etp_key])
            ->andFilterWhere(['like', 'etp_name', $this->etp_name])
            ->andFilterWhere(['like', 'etp_origin_name', $this->etp_origin_name]);

        return $dataProvider;
    }
}
