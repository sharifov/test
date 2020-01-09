<?php

use common\models\SettingCategory;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Setting Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Setting Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'sc_id',
            'sc_name',
            [
                'attribute' => 'sc_enabled',
                'value' => static function (SettingCategory $model) {
                    return '<span class="label label-' . (boolval($model->sc_enabled) ? 'success' : 'danger') . '">' .
                        (boolval($model->sc_enabled) ? 'true' : 'false') . '</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'sc_created_dt',
                'value' => static function (SettingCategory $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->sc_created_dt));
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'sc_updated_dt',
                'value' => static function (SettingCategory $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->sc_created_dt));
                },
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
