<?php

use common\models\ClientEmail;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use sales\helpers\email\MaskEmailHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ClientEmailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Emails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-email-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Email', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['timeout' => 5000]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Client',
                'attribute' => 'client_id',
                'value' => static function (ClientEmail $model) {
                    $client = $model->client;
                    if ($client->id) {
                        return '<span class="label label-info"> <i class="fa fa-link"></i> ' . Html::encode($client->full_name) . ' (' . $client->id . ')</span>';
                    } else {
                        return 'not set';
                    }
                },
                'format' => 'raw'
            ],
            //'email:email',
            [
                'attribute' => 'email',
                'value' => static function (\common\models\ClientEmail $model) {
                    return MaskEmailHelper::masking($model->email);
                },
                'format' => 'email'
            ],
            'ce_title',
            'comments:text',
            /*[
                'attribute' => 'created',
                'value' => static function (ClientEmail $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'created'
            ],

            /*[
                'attribute' => 'updated',
                'value' => static function (ClientEmail $model) {
                    return $model->updated ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) : null;
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'updated'
            ],

            [
                'attribute' => 'type',
                'filter' => ClientEmail::EMAIL_TYPE,
                'value' => static function (ClientEmail $clientEmail) {
                    return ClientEmail::getEmailTypeLabel($clientEmail->type);
                },
                'format' => 'raw',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
