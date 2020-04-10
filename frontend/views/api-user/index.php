<?php

use common\components\grid\DateTimeColumn;
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

    <div class="card card-default">
        <div class="card-body">

            <p>
                <?= Html::a('Create Api User', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'au_id',
                    'au_name',
                    'au_api_username',
                    //'au_api_password',
                    'au_email:email',
                    'au_enabled:boolean',
                    [
                        'class' => DateTimeColumn::class,
                        'attribute' => 'au_updated_dt',
                    ],
                    'auProject.name',
                    /*[
                            'attribute' => 'auUpdatedUser.username',
                    ]*/
                    'au_rate_limit_number',
                    'au_rate_limit_reset',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
