<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadFlightSegment;

/**
 * LeadFlightSegmentSearch represents the model behind the search form of `common\models\LeadFlightSegment`.
 */
class LeadFlightSegmentSearch extends LeadFlightSegment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'lead_id', 'flexibility'], 'integer'],
            [['origin', 'destination', 'departure', 'created', 'updated', 'flexibility_type'], 'safe'],
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
        $query = LeadFlightSegment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            var_dump($this->getErrors());
            return $dataProvider;
        }*/

        if (isset($params['LeadFlightSegmentSearch']['created'])) {
            $query->andFilterWhere(['=','DATE(created)', $this->created]);
        }

        if (isset($params['LeadFlightSegmentSearch']['updated'])) {
            $query->andFilterWhere(['=','DATE(updated)', $this->updated]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'departure' => $this->departure,
            'flexibility' => $this->flexibility,
            'flexibility_type' => $this->flexibility_type,
        ]);

        $query->andFilterWhere(['like', 'origin', $this->origin])
            ->andFilterWhere(['like', 'destination', $this->destination]);
            //->andFilterWhere(['like', 'flexibility_type', $this->flexibility_type])
            /*->andFilterWhere(['like', 'origin_label', $this->origin_label])
            ->andFilterWhere(['like', 'destination_label', $this->destination_label]);*/

        return $dataProvider;
    }
}
