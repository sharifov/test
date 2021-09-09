<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadQcallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Qcalls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-qcall-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Qcall', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Flush Redis reservation', ['flush'], ['class' => 'btn btn-danger', 'data' => [
            'confirm' => 'Are you sure you want to flush data?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lqc_lead_id',
            'lqc_weight',
            /*[
                'attribute' => 'lqc_dt_from',
                'value' => static function (\common\models\LeadQcall $model) {
                    return $model->lqc_dt_from ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_from)) : '-';
                },
                'format' => 'raw'
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lqc_dt_from'
            ],

            /*[
                'attribute' => 'lqc_dt_to',
                'value' => static function (\common\models\LeadQcall $model) {
                    return $model->lqc_dt_to ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_to)) : '-';
                },
                'format' => 'raw'
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lqc_dt_to'
            ],

            [
                'label' => 'Duration',
                'value' => static function (\common\models\LeadQcall $model) {
                    return Yii::$app->formatter->asDuration(strtotime($model->lqc_dt_to) - strtotime($model->lqc_dt_from));
                },
            ],

            [
                'label' => 'Deadline',
                'value' => static function (\common\models\LeadQcall $model) {
                    $timeTo = strtotime($model->lqc_dt_to);
                    return time() <= $timeTo ? Yii::$app->formatter->asDuration($timeTo - time()) : 'deadline';
                },
            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
