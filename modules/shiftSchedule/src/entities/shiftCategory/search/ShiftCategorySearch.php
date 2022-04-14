<?php

namespace modules\shiftSchedule\src\entities\shiftCategory\search;

use modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ShiftCategorySearch represents the model behind the search form of `shiftCategory`.
 */
class ShiftCategorySearch extends ShiftCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_created_user_id', 'sc_updated_user_id'], 'integer'],
            [['sc_name', 'sc_created_dt', 'sc_updated_dt'], 'safe'],
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
        $query = ShiftCategory::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'sc_id' => $this->sc_id,
            'sc_created_user_id' => $this->sc_created_user_id,
            'sc_updated_user_id' => $this->sc_updated_user_id,
            'date(sc_created_dt)' => $this->sc_created_dt,
            'date(sc_updated_dt)' => $this->sc_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'sc_name', $this->sc_name]);

        return $dataProvider;
    }
}
