<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\UserFailedLoginSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
            'ufl_user_id',
            'ufl_ua',
            'ufl_ip',
            //'ufl_session_id',
            //'ufl_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
