<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApiUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Api Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="panel panel-default">
        <div class="panel-body panel-collapse collapse in">

            <p>
                <?= Html::a('Create Api User', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    'au_id',
                    'au_name',
                    'au_api_username',
                    //'au_api_password',
                    'au_email:email',
                    'au_enabled:boolean',
                    'au_updated_dt',
                    'auProject.name',
                    /*[
                            'attribute' => 'auUpdatedUser.username',
                    ]*/

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
