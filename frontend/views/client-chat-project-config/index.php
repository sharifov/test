<?php

use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\entity\projectConfig\search\ClientChatProjectConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Project Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-project-config-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat Project Config', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'ccpc_project_id',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'ccpc_project_id',
                'relation' => 'ccpcProject',
            ],
            'ccpc_params_json',
            'ccpc_theme_json',
            //'ccpc_registration_json',
            //'ccpc_settings_json',
            'ccpc_enabled:boolean',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccpc_created_user_id',
                'relation' => 'ccpcCreatedUser',
                'placeholder' => 'Select User'
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccpc_updated_user_id',
                'relation' => 'ccpcUpdatedUser',
                'placeholder' => 'Select User'
            ],
//            'ccpc_created_user_id',
//            'ccpc_updated_user_id',
            'ccpc_created_dt:byUserDateTime',
            'ccpc_updated_dt:byUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
