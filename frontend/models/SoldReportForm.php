<?php

namespace frontend\models;

use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * SoldReportForm form
 */
class SoldReportForm extends Model
{
    public $dateFrom;
    public $dateTo;
    public $employee;

    public $totalCount = 0;
    public $limit = 100;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dateFrom', 'dateTo', 'employee'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dateTo' => 'Date To',
            'employee' => 'Employee',
            'dateFrom' => 'Date From',
        ];
    }

    public function afterValidate()
    {
        $this->dateFrom = !empty($this->dateFrom) ? date('Y-m-d 00:00:00', strtotime($this->dateFrom)) : '';
        $this->dateTo = !empty($this->dateTo) ? date('Y-m-d 23:59:59', strtotime($this->dateTo)) : '';

        if (!empty($this->dateFrom) && empty($this->dateTo)) {
            $this->dateTo = date('Y-m-d 23:59:59', strtotime($this->dateFrom));
        } elseif (!empty($this->dateTo) && empty($this->dateFrom)) {
            $this->dateFrom = date('Y-m-d 00:00:00', strtotime($this->dateTo));
        }

        if (empty($this->dateFrom) && empty($this->dateTo)) {
            $this->addError('dateFrom', 'Cannot be blank');
            $this->addError('dateTo', 'Cannot be blank');
        }

        parent::afterValidate();
    }

    public function search()
    {
        $result = [];
        if (!$this->validate()) {
            return $result;
        }

        $query = Lead::find();
        $query->where('updated BETWEEN :firstDate AND :lastDate', [
            ':firstDate' => $this->dateFrom, ':lastDate' => $this->dateTo,
        ])->andWhere(['status' => Lead::STATUS_SOLD]);

        $this->getCriteria($query);
        $items = $query->all();

        foreach ($items as $key => $item) {
            /**
             * @var $item Lead
             */
            $result[] = $this->getItem($item);
        }

        $agents = ArrayHelper::index($result, null, 'agent');

        $result = [];
        foreach ($agents as $key => $items) {
            $data = [
                'agent' => $key,
                'totalSold' => 0,
                'pax' => 0,
                'totalProfit' => 0,
                'fromInbox' => 0,
                'fromFollowUp' => 0,
                'personalCreated' => 0,
                'ids' => []
            ];
            foreach ($items as $item) {
                $data['totalSold'] = $data['totalSold'] + $item['totalSold'];
                $data['totalProfit'] = $data['totalProfit'] + $item['totalProfit'];
                $data['fromInbox'] = $data['fromInbox'] + $item['fromInbox'];
                $data['fromFollowUp'] = $data['fromFollowUp'] + $item['fromFollowUp'];
                $data['personalCreated'] = $data['personalCreated'] + $item['personalCreated'];
                $data['pax'] = $data['pax'] + $item['pax'];
                $data['ids'][] = $item['id'];
            }

            $result[] = $data;
        }

        $this->totalCount = count($result);
        ArrayHelper::multisort($result, ['totalProfit'], [SORT_DESC]);
        return $result;
    }

    /**
     * @param Query $query
     */
    private function getCriteria(&$query)
    {
        if (!empty($this->employee)) {
            $query->andWhere([Lead::tableName() . '.employee_id' => $this->employee]);
        }
    }

    private function getItem(Lead $item)
    {
        $data = [
            'agent' => $item->employee->username,
            'totalSold' => 1,
            'pax' => 0,
            'totalProfit' => 0,
            'fromInbox' => 0,
            'fromFollowUp' => 0,
            'personalCreated' => 0,
            'id' => $item->id
        ];

        if ($item->getFinalProfit()) {
            $quote = $item->getAppliedAlternativeQuotes();
            if ($quote !== null) {
                $price = $quote->quotePrice();
                $data['totalProfit'] = Quote::getProfit($price['mark_up'], $price['selling'], $price['fare_type'], $price['isCC']);
            }
        } else {
            $data['totalProfit'] = (float) $item->getFinalProfit();
        }

        $data['pax'] = ($item->adults + $item->children + $item->infants);

        $transitions = $item->getFlowTransition();
        if (!empty($transitions)) {
            $mapping = ArrayHelper::map($transitions, 'id', 'status');
            if (in_array(Lead::STATUS_FOLLOW_UP, $mapping)) {
                $data['fromFollowUp'] = 1;
            } else if (!in_array(Lead::STATUS_FOLLOW_UP, $mapping) && !in_array(Lead::STATUS_PENDING, $mapping)) {
                $data['personalCreated'] = 1;
            } else {
                $data['fromInbox'] = 1;
            }
        } else {
            $data['fromFollowUp'] = 1;
        }

        return $data;
    }

    public function getColumns($dataProvider)
    {
        return [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Agent',
                'value' => function ($model) {
                    return $model['agent'];
                }
            ],
            [
                'label' => 'Sold',
                'value' => function ($model) {
                    $url = Url::to([
                        'report/view-sold',
                        'ids' => implode(',', $model['ids'])
                    ]);
                    return Html::a($model['totalSold'], '#', [
                        'class' => 'view-detail-sold',
                        'data-pjax' => 0,
                        'data-url' => $url
                    ]);
                },
                'format' => 'raw',
                'footer' => self::getTotal($dataProvider->models, 'totalSold'),
            ],
            [
                'label' => 'PAX',
                'value' => function ($model) {
                    return '<i class="fa fa-male"></i> ' . $model['pax'];
                },
                'format' => 'raw',
                'footer' => '<i class="fa fa-male"></i> ' . self::getTotal($dataProvider->models, 'pax'),
            ],
            [
                'label' => 'Avg per PAX',
                'value' => function ($model) {
                    return '<i class="fa fa-dollar"></i> ' . number_format(($model['totalProfit'] / $model['pax']), 2);
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Profit',
                'value' => function ($model) {
                    return '<i class="fa fa-dollar"></i> ' . number_format($model['totalProfit'], 2);
                },
                'format' => 'raw',
                'footer' => '<i class="fa fa-dollar"></i> ' . number_format(self::getTotal($dataProvider->models, 'totalProfit'), 2),
            ],
            [
                'label' => 'Inbox -> Processing',
                'value' => function ($model) {
                    return $model['fromInbox'];
                },
                'footer' => self::getTotal($dataProvider->models, 'fromInbox'),
            ],
            [
                'label' => 'Follow Up -> Processing',
                'value' => function ($model) {
                    return $model['fromFollowUp'];
                },
                'footer' => self::getTotal($dataProvider->models, 'fromFollowUp'),
            ],
            [
                'label' => 'Created',
                'value' => function ($model) {
                    return $model['personalCreated'];
                },
                'footer' => self::getTotal($dataProvider->models, 'personalCreated'),
            ],
        ];
    }

    private static function getTotal($provider, $columnName)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$columnName];
        }
        return $total;
    }
}
