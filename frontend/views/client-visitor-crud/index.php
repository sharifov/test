<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientVisitor\entity\ClientVisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Visitors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-visitor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Visitor', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-visitor']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'cv_client_id:client',
            'cv_visitor_id',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cv_created_dt'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
