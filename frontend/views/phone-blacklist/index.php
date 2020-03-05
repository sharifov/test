<?php

use common\models\PhoneBlacklist;
use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

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
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],

        'rowOptions' => static function (PhoneBlacklist $model) {
            if (!$model->pbl_enabled) {
                return ['class' => 'danger'];
            }
            if ($model->pbl_expiration_date && strtotime($model->pbl_expiration_date) < time()) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            'pbl_id',
            'pbl_phone',
            'pbl_description',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'pbl_enabled',
            ],
            [
                'label' => 'Expiration date',
                'attribute' => 'pbl_expiration_date',
                'value' => static function (PhoneBlacklist $model) {
                    return $model->pbl_expiration_date ?
                        '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDate(strtotime($model->pbl_expiration_date)) :
                        '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'pbl_expiration_date',
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
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'pbl_created_dt',
//            ],
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
