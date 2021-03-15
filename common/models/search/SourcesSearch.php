<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Sources;

/**
 * SourcesSearch represents the model behind the search form of `common\models\Sources`.
 */
class SourcesSearch extends Sources
{
    public bool $only_duplicate = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'default', 'hidden'], 'integer'],
            [['name', 'cid', 'last_update'], 'safe'],
            [['last_update'], 'date', 'format' => 'php:Y-m-d'],

            ['only_duplicate', 'boolean'],
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
        $query = Sources::find()->with('project');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['hidden' => SORT_ASC, 'last_update' => SORT_DESC]],
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

        if ($this->last_update) {
            $query->andFilterWhere(['>=', 'last_update', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_update))])
                ->andFilterWhere(['<=', 'last_update', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_update) + 3600 * 24)]);
        }

        if ($this->only_duplicate) {
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand('
                SELECT
                    sources.id
                FROM
                    sources
                INNER JOIN (
                    SELECT
                        cid, project_id, COUNT(*) AS cnt
                    FROM
                        sources
                    GROUP BY
                        cid, project_id
                ) AS duplicate_sources
                ON duplicate_sources.cid = sources.cid
                AND duplicate_sources.project_id = sources.project_id
                AND duplicate_sources.cnt > 1
            ');
            $subQuery = $command->queryAll();

            $query->andWhere(['IN', 'id', $subQuery]);
            $dataProvider->setSort(['defaultOrder' => ['cid' => SORT_ASC]]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'default' => $this->default,
            'hidden' => $this->hidden,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'cid', $this->cid]);

        return $dataProvider;
    }
}
