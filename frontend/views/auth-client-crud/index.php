<?php

use sales\model\authClient\entity\AuthClient;
use sales\model\authClient\entity\AuthClientSources;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\authClient\entity\AuthClientSearch */
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
                'attribute' => 'ac_id',
                'options' => [
                    'width' => '100px;'
                ]
            ],
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'ac_user_id',
                'relation' => 'user',
            ],
            [
                'attribute' => 'ac_source',
                'value' => static function (AuthClient $model) {
                    return AuthClientSources::getName($model->ac_source);
                },
                'filter' => AuthClientSources::getList()
            ],
            'ac_source_id',
            'ac_email:email',
            'ac_ip',
            'ac_useragent',
            'ac_created_dt:byUserDateTime',

            ['class' => 'yii\grid\ActionColumn', 'urlCreator' => static function (string $action, $model, $key, $index) {
                return \yii\helpers\Url::to([$action, 'ac_id' => $model->ac_id]);
            }],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
