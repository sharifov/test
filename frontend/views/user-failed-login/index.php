<?php

use common\components\grid\DateTimeColumn;
use yii\grid\SerialColumn;
use frontend\models\UserFailedLogin;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var frontend\models\search\UserFailedLoginSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Failed Logins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-failed-login-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Failed Login', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            'ufl_id',
            'ufl_username',
            'ufl_user_id:userName',
            'ufl_ua',
            'ufl_ip',
            'ufl_active:boolean',
            'ufl_session_id',
            ['class' => DateTimeColumn::class, 'attribute' => 'ufl_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>


</div>
