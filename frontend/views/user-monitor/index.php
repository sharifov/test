<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\auth\Auth;
use sales\model\user\entity\monitor\UserMonitor;
use sales\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\monitor\search\UserMonitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'User Monitors';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-user-monitor';
?>
<div class="user-monitor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Monitor', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Auth::can('global/clean/table')): ?>
        <div class="col-md-6" style="margin-left: -10px;">
            <?php echo $this->render('../clean/_clean_table_form', [
                'modelCleaner' => $modelCleaner,
                'pjaxIdForReload' => $pjaxListId,
            ]); ?>
        </div>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'um_id',
            //'um_user_id',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'um_user_id',
                'relation' => 'umUser',
                'placeholder' => 'Select User'
            ],
            //'um_type_id',
            [
                'attribute' => 'um_type_id',
                'value' => static function (UserMonitor $model) {
                    return $model->getTypeName();
                },
                'filter' => UserMonitor::getTypeList(),

            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'um_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'um_end_dt',
            ],
//            'um_start_dt',
//            'um_end_dt',
            'um_period_sec',
            'um_description',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
