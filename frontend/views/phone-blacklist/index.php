<?php

use frontend\extensions\grid\BooleanColumn;
use frontend\extensions\grid\DateTimeColumn;
use frontend\extensions\grid\UserColumn;
use sales\formatters\WidgetFormatter;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PhoneBlacklistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Blacklists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-blacklist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Blacklist', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'formatter' => ['class' => WidgetFormatter::class],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'pbl_id',
            'pbl_phone',
            'pbl_description',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'pbl_enabled',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pbl_created_dt',
                'searchModel' => $searchModel,
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pbl_updated_dt',
                'searchModel' => $searchModel,
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'pbl_updated_user_id',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
