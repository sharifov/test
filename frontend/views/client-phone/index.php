<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ClentPhoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Phones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-phone-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Client Phone', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Client',
                'attribute' => 'client_id',
                'value' => function (\common\models\ClientPhone $model) {
                    $client = $model->client;
                    if($client->id) {
                        return '<span class="label label-info"> <i class="fa fa-link"></i> ' .  Html::encode($client->full_name). '</span>';
                    } else {
                        return 'not set';
                    }
                },
                'format' => 'raw'
            ],
            //'client_id',
            'phone',
            'created',
            //'updated',
            //'comments:ntext',
            'is_sms',
            'validate_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
