<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
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
        <div class="card-body card-collapse collapse show">

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
                        'attribute' => 'au_updated_dt',
                        'value' => static function (\common\models\ApiUser $model) {
                            return $model->au_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->au_updated_dt)) : '-';
                        },
                        'format' => 'raw',
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'au_updated_dt',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                            ],
                            'options' => [
                                'autocomplete' => 'off',
                                'placeholder' =>'Choose Date'
                            ],
                        ]),
                    ],
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
