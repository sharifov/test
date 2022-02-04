<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Poor Processing Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-poor-processing-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-lead-poor-processing-data']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'lppd_id',
            [
                'attribute' => 'lppd_enabled',
                'value' => static function (LeadPoorProcessingData $model) {
                    return Yii::$app->formatter->asBooleanByLabel($model->lppd_enabled);
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            [
                'attribute' => 'lppd_key',
                'value' => static function (LeadPoorProcessingData $model) {
                    return '<i class="fa fa-key"></i> ' . $model->lppd_key;
                },
                'filter' => LeadPoorProcessingDataQuery::getList(60),
                'format' => 'raw',
            ],
            'lppd_name',
            'lppd_description',
            [
                'attribute' => 'lppd_minute',
                'value' => static function (LeadPoorProcessingData $model) {
                    return '<i class="fa fa-clock-o"></i> ' . $model->lppd_minute . ' minutes';
                },
                'format' => 'raw',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => static function ($url, LeadPoorProcessingData $model) {
                        return Html::a(
                            '<i class="fa fa-eye"></i>',
                            ['/lead-poor-processing-data-crud/view', 'lppd_id' => $model->lppd_id],
                            ['data-pjax' => 0,]
                        );
                    },
                    'update' => static function ($url, LeadPoorProcessingData $model) {
                        return Html::a(
                            '<i class="fa fa-pencil"></i>',
                            ['/lead-poor-processing-data-crud/update', 'lppd_id' => $model->lppd_id],
                            ['data-pjax' => 0,]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
