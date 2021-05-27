<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\appProjectKey\entity\AppProjectKey;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\appProjectKey\entity\AppProjectKeySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'App Project Keys';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-project-key-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create App Project Key', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-app-project-key']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'apk_id',
            'apk_key',
            'apk_project_id:projectName',
            [
                'attribute' => 'apk_project_source_id',
                'format' => 'raw',
                'value' => static function (AppProjectKey $model) {
                    if (!$model->apkProjectSource) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return $model->apkProjectSource->name;
                },
                'filter' => \common\models\Sources::getList(true),
                'label' => 'Project Source Name',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'apk_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'apk_updated_dt',
                'format' => 'byUserDateTime'
            ],
            ['class' => UserSelect2Column::class, 'attribute' => 'apk_created_user_id', 'relation' => 'apkCreatedUser'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
