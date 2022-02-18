<?php

use src\auth\Auth;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\services\cleaner\form\DbCleanerParamsForm;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'Lead Poor Processing Logs';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-lpp-log';
?>
<div class="lead-poor-processing-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Poor Processing Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php if (Auth::can('global/clean/table')) : ?>
        <?php echo $this->render('../clean/_clean_table_form', [
            'modelCleaner' => $modelCleaner,
            'pjaxIdForReload' => $pjaxListId,
        ]); ?>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId, 'scrollTo' => 0]); ?>
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
                'filter'  => LeadPoorProcessingLogStatus::STATUS_LIST,
                'value' => static function (LeadPoorProcessingLog $model) {
                    return $model->getStatusName();
                },
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'lppl_owner_id',
                'relation' => 'owner',
                'placeholder' => 'Owner'
            ],
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
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lppl_created_dt',
                'limitEndDay' => false,
            ],

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
