<?php

namespace modules\objectSegment\src\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\objectSegment\src\entities\ObjectSegmentTask;

/**
 * ObjectSegmentTaskSearch represents the model behind the search form of `modules\objectSegment\src\entities\ObjectSegmentTask`.
 */
class ObjectSegmentTaskSearch extends ObjectSegmentTask
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ostl_osl_id', 'ostl_tl_id', 'ostl_created_user_id'], 'integer'],
            [['ostl_created_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
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
        $query = ObjectSegmentTask::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ostl_created_dt' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ostl_osl_id' => $this->ostl_osl_id,
            'ostl_tl_id' => $this->ostl_tl_id,
            'date_format(ostl_created_dt, "%Y-%m-%d")' => $this->ostl_created_dt,
            'ostl_created_user_id' => $this->ostl_created_user_id,
        ]);

        return $dataProvider;
    }
}
