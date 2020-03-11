<?php

use sales\model\user\entity\userStatus\UserStatus;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\userStatus\UserStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-status-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
    <div class="col-md-12">
        Site settings "general_line_last_hours":  <b><?=(Yii::$app->params['settings']['general_line_last_hours'] ?? 1)?></b> for "<b><?=Html::encode((new UserStatus)->getAttributeLabel('us_gl_call_count'))?></b>"
    </div>
    <hr>
    <div class="col-md-12">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['label' => 'User Id',
                'value' => static function(UserStatus $model) {
                    return $model->us_user_id;
                },
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'us_user_id',
                'relation' => 'usUser',
            ],

            'us_gl_call_count',
            'us_call_phone_status:boolean',
            'us_is_on_call:boolean',
            'us_has_call_access:boolean',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'us_updated_dt',
            ],



            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
    </div>
    </div>

</div>
