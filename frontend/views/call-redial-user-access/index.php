<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadRedial\entity\search\CallRedialUserAccessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Redial User Accesses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-redial-user-access-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Redial User Access', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-call-redial-user-access', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'crua_lead_id',
            'crua_user_id',
            ['class' => DateTimeColumn::class, 'attribute' => 'crua_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'crua_updated_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
