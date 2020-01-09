<?php

use common\models\SettingCategory;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SettingCategory */

$this->title = $model->sc_name;
$this->params['breadcrumbs'][] = ['label' => 'Setting Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="setting-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->sc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->sc_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
        ],
    ]) ?>

</div>
