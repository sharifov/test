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
                $data['ids'][] = $item['id'];
            }

            $result[] = $data;
        }

        $this->totalCount = count($result);
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
            'totalProfit' => 0,
            'fromInbox' => 0,
            'fromFollowUp' => 0,
            'personalCreated' => 0,
            'id' => $item->id
        ];

        $quote = $item->getAppliedAlternativeQuotes();
        if ($quote !== null) {
            $price = $quote->quotePrice();
            $data['totalProfit'] = ($price['selling'] * Quote::SERVICE_FEE);
        }

        $transitions = $item->getFlowTransition();
        if (!empty($transitions)) {
            $mapping = ArrayHelper::map($transitions, 'id', 'status');
            if (!in_array(Lead::STATUS_FOLLOW_UP, $mapping) && !in_array(Lead::STATUS_PENDING, $mapping)) {
                $data['personalCreated'] = 1;
            } else if (in_array(Lead::STATUS_FOLLOW_UP, $mapping)) {
                $data['fromFollowUp'] = 1;
            } else {
                $data['fromInbox'] = 1;
            }
        } else {
            $data['fromFollowUp'] = 1;
        }

        return $data;
    }

    public function getColumns()
    {
        return [
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
                'format' => 'raw'
            ],
            [
                'label' => 'Profit',
                'value' => function ($model) {
                    return '$' . number_format($model['totalProfit'], 2);
                }
            ],
            [
                'label' => 'Inbox -> Processing',
                'value' => function ($model) {
                    return $model['fromInbox'];
                }
            ],
            [
                'label' => 'Follow Up -> Processing',
                'value' => function ($model) {
                    return $model['fromFollowUp'];
                }
            ],
            [
                'label' => 'Created',
                'value' => function ($model) {
                    return $model['personalCreated'];
                }
            ],
        ];
    }
}
