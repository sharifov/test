<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\callRecordingLog\entity\search\CallRecordingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Recording Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-recording-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Recording Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-call-recording-log']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'crl_id',
            'crl_call_sid',
            [
                'class' => UserSelect2Column::class,
                'relation' => 'user',
                'attribute' => 'crl_user_id'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'crl_created_dt',
            ],
            'crl_year',
            'crl_month',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
