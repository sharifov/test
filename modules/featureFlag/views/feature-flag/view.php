<?php

use modules\featureFlag\src\entities\FeatureFlag;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\featureFlag\src\entities\FeatureFlag */

$this->title = $model->ff_id;
$this->params['breadcrumbs'][] = ['label' => 'Feature Flags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="feature-flag-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('<i class="fa fa-pencil"></i> Update', ['update', 'ff_id' => $model->ff_id], ['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ff_id',
                'ff_key',
                'ff_name',
                [
                    'attribute' => 'ff_value',
                    'value' => static function (FeatureFlag $model) {

                        $val = Html::encode($model->ff_value);

                        if ($model->ff_type == FeatureFlag::TYPE_BOOL) {
                            $val = $model->ff_value ? '<span class="label label-success">true</span>' : '<span class="label label-danger">false</span>';
                        }

                        if ($model->ff_type == FeatureFlag::TYPE_ARRAY) {
                            $val = '<pre><small>' . ($model->ff_value ? VarDumper::dumpAsString(@json_decode($model->ff_value, true), 10, false) : '-') . '</small></pre>';
                        }

                        return $val;
                    },
                    'format' => 'raw',
                    //'filter' => false
                ],
                'ff_category',
                'ff_description',
                'ff_type',
//                [
//                    'attribute' => 'ff_updated_user_id',
//                    'value' => static function (FeatureFlag $model) {
//                        return ($model->sUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->sUpdatedUser->username) : $model->ff_updated_user_id);
//                    },
//                    'format' => 'raw',
//                ],

                [
                    'attribute' => 'ff_updated_dt',
                    'value' => static function (FeatureFlag $model) {
                        return $model->ff_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ff_updated_dt)) : $model->ff_updated_dt;
                    },
                    'format' => 'raw'
                ],



                //'ff_type',
                //'ff_value',

                //'ff_description',
                'ff_enable_type',
                'ff_attributes',
                'ff_condition',
                //'ff_updated_dt',
                //'ff_updated_user_id',
            ],
        ]) ?>

    </div>

</div>
