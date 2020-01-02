<?php

use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use sales\yii\i18n\Formatter;
use yii\grid\ActionColumn;
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
        'columns' => [
            'pbl_id',
            'pbl_phone',
            'pbl_description',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'pbl_enabled',
                'format' => 'raw'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pbl_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pbl_updated_dt',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'pbl_updated_user_id',
                'relation' => 'updatedUser',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
