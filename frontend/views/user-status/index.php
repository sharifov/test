<?php

use sales\model\user\entity\userStatus\UserStatus;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\userStatus\search\UserStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-status-index">

    <h1><i class="fa fa-sliders"></i> <?= Html::encode($this->title) ?></h1>

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
            'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            'rowOptions' => static function (UserStatus $model) {
                if (!$model->us_call_phone_status) {
                    return ['class' => 'danger'];
                }

                if ($model->us_is_on_call) {
                    return ['class' => 'warning'];
                }

                if ($model->us_has_call_access) {
                    return ['class' => 'warning'];
                }
            },
            'columns' => [

                /*['label' => 'User Id',
                    'value' => static function(UserStatus $model) {
                        return $model->us_user_id;
                    },
                ],
                [
                    'class' => UserColumn::class,
                    'attribute' => 'us_user_id',
                    'relation' => 'usUser',
                ],*/

                [
                    'class' => \common\components\grid\UserSelect2Column::class,
                    'attribute' => 'us_user_id',
                    'relation' => 'usUser',
                    'placeholder' => 'Select User',
                ],

                'us_gl_call_count',
                'us_call_phone_status:boolean',
                'us_is_on_call:boolean',
                [
                    'attribute' => 'online',
                    'filter' => [1 => 'Online', 2 => 'Offline'],
                    'value' => static function (UserStatus $model) {
                        return $model->usUser->isOnline() ? '<span class="label label-success">Online</span>' : '<span class="label label-danger">Offline</span>';
                    },
                    'format' => 'raw'
                ],
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
