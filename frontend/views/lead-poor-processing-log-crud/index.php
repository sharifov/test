<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
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
            'lppl_lead_id',
            [
                'attribute' => 'lppl_lppd_id',
                'value' => static function (LeadPoorProcessingLog $model) {
                    return $model->lppl_lppd_id;
                },
                'filter' => LeadPoorProcessingDataQuery::getList(60),
            ],
            [
                'attribute' => 'lppl_status',
                'value' => static function (LeadPoorProcessingLog $model) {
                    return $model->getStatusName();
                },
            ],
            'lppl_owner_id:userName',
            'lppl_created_dt:byUserDatetime',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
