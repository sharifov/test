<?php

use common\components\grid\DateTimeColumn;
use sales\model\userAuthClient\entity\UserAuthClient;
use sales\model\userAuthClient\entity\UserAuthClientSources;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\userAuthClient\entity\UserAuthClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Auth Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-client-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Auth Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'uac_id',
                'options' => [
                    'width' => '100px;'
                ]
            ],
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'uac_user_id',
                'relation' => 'user',
            ],
            [
                'attribute' => 'uac_source',
                'value' => static function (UserAuthClient $model) {
                    return UserAuthClientSources::getName($model->uac_source);
                },
                'filter' => UserAuthClientSources::getList()
            ],
            'uac_source_id',
            'uac_email:email',
            'uac_ip',
            'uac_useragent',
            ['class' => DateTimeColumn::class, 'attribute' => 'uac_created_dt'],

            ['class' => 'yii\grid\ActionColumn', 'urlCreator' => static function (string $action, $model, $key, $index) {
                return \yii\helpers\Url::to([$action, 'uac_id' => $model->uac_id]);
            }],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
