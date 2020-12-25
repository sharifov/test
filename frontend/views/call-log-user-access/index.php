<?php

use common\components\grid\DateTimeColumn;
use common\models\CallUserAccess;
use sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\callLog\entity\callLogUserAccess\search\CallLogUserAccessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Log User Accesses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-user-access-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Log User Access', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-call-log-user-access']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'clua_id',
            'clua_cl_id',
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'clua_user_id',
                'relation' => 'user',
            ],
            [
                'attribute' => 'clua_access_status_id',
                'filter' => CallUserAccess::getStatusTypeList(),
                'value' => static function (CallLogUserAccess $model) {
                    return CallUserAccess::getStatusTypeList()[$model->clua_access_status_id] ?? null;
                },
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'clua_access_start_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'clua_access_finish_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
