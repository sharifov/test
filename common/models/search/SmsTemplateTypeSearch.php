<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SmsTemplateType;

/**
 * SmsTemplateTypeSearch represents the model behind the search form of `common\models\SmsTemplateType`.
 */
class SmsTemplateTypeSearch extends SmsTemplateType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stp_id', 'stp_hidden', 'stp_created_user_id', 'stp_updated_user_id'], 'integer'],
            [['stp_key', 'stp_origin_name', 'stp_name', 'stp_created_dt', 'stp_updated_dt'], 'safe'],
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
        $query = SmsTemplateType::find()->with('stpCreatedUser', 'stpUpdatedUser');

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

        if ($this->stp_created_dt) {
            $query->andFilterWhere(['>=', 'stp_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->stp_created_dt))])
                ->andFilterWhere(['<=', 'stp_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->stp_created_dt) + 3600 * 24)]);
        }

        if ($this->stp_updated_dt) {
            $query->andFilterWhere(['>=', 'stp_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->stp_updated_dt))])
                ->andFilterWhere(['<=', 'stp_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->stp_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'stp_id' => $this->stp_id,
            'stp_hidden' => $this->stp_hidden,
            'stp_created_user_id' => $this->stp_created_user_id,
            'stp_updated_user_id' => $this->stp_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'stp_key', $this->stp_key])
            ->andFilterWhere(['like', 'stp_origin_name', $this->stp_origin_name])
            ->andFilterWhere(['like', 'stp_name', $this->stp_name]);

        return $dataProvider;
    }
}
