<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Poor Processing Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-poor-processing-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Poor Processing Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-poor-processing-log']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'lppl_id',
            [
                'attribute' => 'lppl_lead_id',
                'value' => static function (LeadPoorProcessingLog $model) {
                    return Yii::$app->formatter->asLead($model->lpplLead, 'fa-cubes');
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'lppl_lppd_id',
                'value' => static function (LeadPoorProcessingLog $model) {
                    return '<i class="fa fa-key"></i> ' . $model->lpplLppd->lppd_key;
                },
                'filter' => LeadPoorProcessingDataQuery::getList(60),
                'format' => 'raw',
            ],
            [
                'attribute' => 'lppl_status',
                'value' => static function (LeadPoorProcessingLog $model) {
                    return $model->getStatusName();
                },
            ],
            'lppl_owner_id:userName',
            [
                'attribute' => 'lppl_description',
                'value' => static function (LeadPoorProcessingLog $model) {
                    if (!$model->lppl_description) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return StringHelper::truncate($model->lppl_description, 100, '...');
                },
                'format' => 'raw',
            ],
            'lppl_created_dt:byUserDatetime',

            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => static function ($url, LeadPoorProcessingLog $model) {
                        return Html::a(
                            '<i class="fa fa-eye"></i>',
                            ['/lead-poor-processing-log-crud/view', 'lppl_id' => $model->lppl_id],
                            ['data-pjax' => 0,]
                        );
                    },
                    'update' => static function ($url, LeadPoorProcessingLog $model) {
                        return Html::a(
                            '<i class="fa fa-pencil"></i>',
                            ['/lead-poor-processing-log-crud/update', 'lppl_id' => $model->lppl_id],
                            ['data-pjax' => 0,]
                        );
                    },
                    'delete' => static function ($url, LeadPoorProcessingLog $model) {
                        return Html::a('<i class="fa fa-trash"></i>', ['delete', 'lppl_id' => $model->lppl_id], [
                            'class' => '',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
