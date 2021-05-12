<?php

namespace common\models\search;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use sales\model\project\entity\projectRelation\ProjectRelation;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Project;
use yii\db\Expression;

/**
 * ProjectSearch represents the model behind the search form of `common\models\Project`.
 *
 * @property array|null $related_projects
 */
class ProjectSearch extends Project
{
    public $related_projects;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'closed', 'sort_order'], 'integer'],
            [['name', 'link', 'api_key', 'contact_info'], 'safe'],
            [['email_postfix'], 'string', 'max' => 100],
            [['project_key'], 'string', 'max' => 50],
            [['last_update'], 'date', 'format' => 'php:Y-m-d'],

            ['related_projects', 'integer'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Project::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->last_update) {
            $query->andFilterWhere(['>=', 'last_update', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_update))])
                ->andFilterWhere(['<=', 'last_update', Employee::convertTimeFromUserDtToUTC(strtotime($this->last_update) + 3600 * 24)]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'closed' => $this->closed,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'contact_info', $this->contact_info])
            ->andFilterWhere(['like', 'project_key', $this->project_key])
            ->andFilterWhere(['like', 'email_postfix', $this->email_postfix]);

        if (!empty($this->related_projects)) {
            $query->innerJoin(ProjectRelation::tableName(), Project::tableName() . '.id = prl_project_id');
            $query->andWhere(['IN', 'prl_related_project_id', $this->related_projects]);
        }

        return $dataProvider;
    }

    public function searchByCallRecording($params): ActiveDataProvider
    {
        $query = static::find();
        $query->andWhere(new Expression('p_params_json->"$.call.call_recording_disabled" = true'));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'project-page',
                'pageSizeParam' => 'project-per-page',
            ],
            'sort' => [
                'sortParam' => 'project-sort',
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
