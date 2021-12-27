<?php

use common\models\Employee;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $searchModel \sales\model\user\entity\monitor\search\UserMonitorSearch */
/* @var $startDateTime string */
/* @var $endDateTime string */

/** @var Employee $user */
$this->title = 'User Data';
$user = Yii::$app->user->identity;
?>
<div class="user-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-user-data']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $userDataProvider,
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'ud_key',
                'format' => 'userDataKey',
            ],
            'ud_value',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'ud_updated_dt',
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>