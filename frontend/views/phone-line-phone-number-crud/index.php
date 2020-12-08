<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\phoneLine\phoneLinePhoneNumber\entity\search\PhoneLinePhoneNumberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Line Phone Numbers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-phone-number-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Line Phone Number', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'plpn_line_id',
            'plpn_pl_id',
            /*[
               'label' => 'Phone Number',
                'value' => static function (\sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber $model) {
                    return $model->plpnPl ? $model->plpnPl->pl_phone_number : '-';
                }
            ],*/
            [
                'attribute' => 'phoneNumber',
                'value' => 'plpnPl.pl_phone_number'
            ],
            'plpn_default:BooleanByLabel',
            'plpn_enabled:BooleanByLabel',
            [
                'attribute' => 'plpn_created_user_id',
                'filter' => \sales\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'plpn_created_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '150px'
                ]
            ],
            [
                'attribute' => 'plpn_updated_user_id',
                'filter' => \sales\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'plpn_updated_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '150px'
                ]
            ],
            /*[
                'attribute' => 'plpn_created_dt',
                'format' => 'byUserDateTime',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'plpn_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date',

                    ],
                ]),
                'options' => [
                    'width' => '150px'
                ]
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'plpn_created_dt'
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'plpn_updated_dt'
            ],

            /*[
                'attribute' => 'plpn_updated_dt',
                'format' => 'byUserDateTime',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'plpn_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
                'options' => [
                    'width' => '150px'
                ]
            ],*/

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
