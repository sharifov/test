<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesStatusLog;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesStatusLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="cases-status-history">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [

            [
                'attribute' => 'csl_id',
                'options' => ['style' => 'width:80px'],
            ],
            [
                'attribute' => 'csl_from_status',
                'value' => static function (CasesStatusLog $model) {
                    return CasesStatus::getLabel($model->csl_from_status); //'<span class="label label-info">' . CasesStatus::getName($model->csl_from_status) . '</span></h5>';
                },
                'format' => 'raw',
                'filter' => CasesStatus::STATUS_LIST,
                //'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'csl_to_status',
                'value' => static function (CasesStatusLog $model) {
                    return CasesStatus::getLabel($model->csl_to_status); //'<span class="label label-info">' . CasesStatus::getName($model->csl_to_status) . '</span></h5>';
                },
                'format' => 'raw',
                'filter' => CasesStatus::STATUS_LIST,
                //'options' => ['style' => 'width:180px'],
            ],
            /*[
                'attribute' => 'csl_case_id',
                'options' => ['style' => 'width:140px'],
            ],*/
            [
                'label' => 'Status start date',
                'attribute' => 'csl_start_dt',
                'value' => static function (CasesStatusLog $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->csl_start_dt));
                },
                'format' => 'raw',
                //'options' => ['style' => 'width:180px'],
            ],
            [
                'label' => 'Status end date',
                'attribute' => 'csl_end_dt',
                'value' => static function (CasesStatusLog $model) {
                    return $model->csl_end_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->csl_end_dt)) : '';
                },///
                'format' => 'raw',
                //'options' => ['style' => 'width:180px'],
            ],
            [
                'label' => 'Duration',
                'attribute' => 'csl_time_duration',
                'value' => static function (CasesStatusLog $model) {
                    return $model->csl_time_duration > -1 ? Yii::$app->formatter->asDuration($model->csl_time_duration) : Yii::$app->formatter->asDuration(time() - strtotime($model->csl_start_dt));
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px'],
            ],
            [
//                'label' => 'Description',
                'attribute' => 'csl_description',
                'value' => static function (CasesStatusLog $model) {
                    return nl2br(Html::encode($model->csl_description));
                },
                'format' => 'raw',
                'options' => ['style' => 'width:280px'],
            ],
            [
                'label' => 'Owner',
                'attribute' => 'csl_owner_id',
                'value' => static function (CasesStatusLog $model) {

                    return $model->owner ? $model->owner->username : '';
                },
            ],
            [
                'label' => 'Created Agent',
                'attribute' => 'csl_created_user_id',
                'value' => static function (CasesStatusLog $model) {
                    return $model->createdUser ? $model->createdUser->username : '';
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
