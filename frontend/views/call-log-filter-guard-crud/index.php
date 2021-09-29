<?php

use common\components\grid\DateTimeColumn;
use common\models\Call;
use sales\model\callLogFilterGuard\entity\CallLogFilterGuard;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\callLogFilterGuard\entity\CallLogFilterGuardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Log Filter Guards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-filter-guard-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php /* echo Html::a('Create Call Log Filter Guard', ['create'], ['class' => 'btn btn-success']) */ ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-call-log-filter-guard']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'clfg_call_id',
            'clfg_cpl_id',
            'clfg_call_log_id',
            [
                'attribute' => 'clfg_type',
                'value' => static function (CallLogFilterGuard $model) {
                    return $model->getTypeName();
                },
                'filter' => CallLogFilterGuard::TYPE_LIST
            ],
            'clfg_sd_rate',
            'clfg_trust_percent',
            [
                'attribute' => 'clfg_redial_status',
                'value' => static function (CallLogFilterGuard $model) {
                    return $model->getRedialStatusName();
                },
                'filter' => Call::STATUS_LIST
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'clfg_created_dt',
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
