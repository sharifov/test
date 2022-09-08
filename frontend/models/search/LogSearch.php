<?php

namespace frontend\models\search;

use frontend\models\LogQuery;
use src\helpers\DateHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Employee;
use frontend\models\Log;

/**
 * LogSearch represents the model behind the search form about `frontend\models\Log`.
 */
class LogSearch extends Log
{
    public $days;
    public $excludedCategories;
    public $createdDateTimeRange;
    public $createdDateTimeStart;
    public $createdDateTimeStartTs;
    public $createdDateTimeEnd;
    public $createdDateTimeEndTs;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'level'], 'integer'],
            [['category', 'prefix', 'message', 'log_time', 'excludedCategories'], 'safe'],
            [['days'], 'integer', 'min' => 0, 'max' => 365],
            ['excludedCategories', 'each', 'rule' => ['string']],
            [['createdDateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            ['createdDateTimeStart', 'datetime', 'format' => 'yyyy-MM-dd HH:mm', 'timestampAttribute' => 'createdDateTimeStartTs', 'defaultTimeZone' => Yii::$app->user->identity->timezone],
            ['createdDateTimeEnd', 'datetime', 'format' => 'yyyy-MM-dd HH:mm', 'timestampAttribute' => 'createdDateTimeEndTs', 'defaultTimeZone' => Yii::$app->user->identity->timezone],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Log::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->searchQuery($query);
        $query->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }

    protected function searchQuery(LogQuery $query, bool $ignoreCategoryFilter = false)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'level' => $this->level,
        ]);

        if ($this->createdDateTimeRange) {
            $query->andFilterWhere([
                'BETWEEN',
                'log_time',
                $this->createdDateTimeStartTs,
                $this->createdDateTimeEndTs
            ]);
        }

        if (empty($this->log_time) === false) {
            $from = Employee::convertTimeFromUserDtToUTC(strtotime($this->log_time));
            $from = strtotime($from);
            $to = strtotime(Employee::convertTimeFromUserDtToUTC(strtotime($this->log_time) + 3600 * 24));
            $query->andOnCondition('log_time >= :from AND log_time <= :to', array(':from' => $from, ':to' => $to));
        }

        if ($ignoreCategoryFilter === false) {
            /** @fflag FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE, Enable improvements in system log search block */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE) === true) {
                $query->andFilterWhere(['category' => $this->category]);
            } else {
                $query->andFilterWhere(['LIKE', 'category', $this->category]);
            }
        }

        $query->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['NOT IN', 'category', $this->excludedCategories]);
    }

    public function getCategoriesFilter(bool $isPjax = true): array
    {
        /** @fflag FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE, Enable improvements in system log search block */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE) === true) {
            $categories = $this->getCategories(true);
        } else {
            $categories = \frontend\models\Log::getCategoryFilter(is_numeric($this->level) ? $this->level : null, $isPjax);
        }

        return $categories;
    }

    public function getCategories(bool $countGroup = false): array
    {
        $arr = [];

        if ($countGroup) {
            $query = self::find();
            $this->searchQuery($query, true);

            $query->select(['COUNT(*) AS cnt', 'category'])
                ->andWhere('category IS NOT NULL')
                ->groupBy(['category'])
                ->orderBy('cnt DESC')
                ->cache(-10)
                ->asArray();

            $data = $query->all();

            if ($data) {
                foreach ($data as $v) {
                    $arr[$v['category']] = $v['category'] . ' - [' . $v['cnt'] . ']';
                }
            }
        } else {
            $query = self::find();
            $this->searchQuery($query, true);
            $query->select('DISTINCT(category) AS category')
                ->cache(-10)
                ->orderBy('category')
                ->asArray();

            $data = $query->all();

            if ($data) {
                foreach ($data as $v) {
                    $arr[$v['category']] = $v['category'];
                }
            }
        }

        return $arr;
    }
}
