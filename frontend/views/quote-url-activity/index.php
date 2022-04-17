<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use common\models\QuoteUrlActivity;
use frontend\models\CommunicationForm;

/* @var $this yii\web\View */
/* @var $searchModel QuoteUrlActivity */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Url Activity';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-url-activity-index">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= Html::tag('p', Html::a('Create Quote Url Activity', ['create'], ['class' => 'btn btn-success'])) ?>

    <?php Pjax::begin(); ?>

    <?php
    try {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'qua_id',
                    'options' => ['width' => '100px']
                ],
                [
                    'attribute' => 'qua_communication_type',
                    'filter' => CommunicationForm::TYPE_LIST,
                    'value' => static function (QuoteUrlActivity $model): string {
                        return (isset(CommunicationForm::TYPE_LIST[$model->qua_communication_type]))
                            ? CommunicationForm::TYPE_LIST[$model->qua_communication_type]
                            : 'Unknown communication type';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qua_status',
                    'filter' => QuoteUrlActivity::statusList(),
                    'value' => static function (QuoteUrlActivity $model): string {
                        $statusName = QuoteUrlActivity::statusName($model->qua_status);
                        return is_null($statusName) ? 'Unknown status' : $statusName;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qua_quote_id',
                    'value' => function (QuoteUrlActivity $model) {
                        return Html::a("<i class=\"fa fa-link\"></i> {$model->qua_quote_id}", ['/quotes/view', 'id' => $model->qua_quote_id], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'qua_created_dt',
                    'format' => 'byUserDateTime',
                    'options' => [
                        'width' => '200px'
                    ],
                ],
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => static function ($action, QuoteUrlActivity $model, $key, $index, $column): string {
                        return Url::toRoute([$action, 'qua_id' => $model->qua_id]);
                    }
                ],
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', $e->getMessage());
    }
    ?>

    <?php Pjax::end(); ?>

</div>
