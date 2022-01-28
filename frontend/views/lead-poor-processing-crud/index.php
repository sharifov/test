<?php

use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadPoorProcessing\entity\LeadPoorProcessingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Poor Processing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-poor-processing-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Poor Processing', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-poor-processing']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'lpp_lead_id:lead',
            'lpp_lppd_id',
            [
                'attribute' => 'lpp_lppd_id',
                'value' => static function (LeadPoorProcessing $model) {
                    return $model->lpp_lppd_id;
                },
                'filter' => LeadPoorProcessingDataQuery::getList(60),
            ],
            'lpp_expiration_dt:byUserDatetime',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
