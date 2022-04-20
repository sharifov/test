<?php

use modules\shiftSchedule\src\entities\shift\Shift;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shift\Shift */

$this->title = $model->sh_name;
$this->params['breadcrumbs'][] = ['label' => 'Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->sh_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->sh_id], [
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
                'sh_id',
                [
                    'attribute' => 'sh_name',
                    'value' => static function (Shift $model) {
                        return $model->getColorLabel() . ' ' . Html::encode($model->sh_name);
                    },
                    'format' => 'raw'
                ],
                'sh_title',
                [
                    'attribute' => 'sh_category_id',
                    'value' => static function (Shift $model) {
                        return $model->category ? Html::encode($model->category->sc_name ?? '') : null;
                    },
                ],
                'sh_enabled:booleanByLabel',
                'sh_color',
                'sh_sort_order',
                'sh_created_dt',
                'sh_updated_dt',
                'sh_created_user_id',
                'sh_updated_user_id',
            ],
        ]) ?>

    </div>

</div>
