<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;

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
            ['class' => 'yii\grid\SerialColumn'],

            'ufl_id',
            'ufl_username',
            'ufl_user_id:userName',
            'ufl_ua',
            'ufl_ip',
            'ufl_active:booleanByLabel',
            'ufl_session_id',
            'ufl_created_dt:byUserDateTime',
            ['class' => ActionColumn::class],
        ],
    ]); ?>


</div>
