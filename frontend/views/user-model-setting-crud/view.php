<?php

use src\model\userModelSetting\entity\UserModelSetting;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var src\model\userModelSetting\entity\UserModelSetting $model */

$this->title = $model->ums_id;
$this->params['breadcrumbs'][] = ['label' => 'User Model Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-model-setting-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-12">
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ums_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ums_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ums_id',
                'ums_user_id:userName',
                'ums_name',
                'ums_key',
                [
                    'attribute' => 'ums_type',
                    'value' => static function (UserModelSetting $model) {
                        return $model->getTypeName();
                    },
                ],
                'ums_class',
                'ums_per_page',
                'ums_enabled:booleanByLabel',
                'ums_created_dt:byUserDateTime',
                'ums_updated_dt:byUserDateTime',
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <strong><?php echo $model->getAttributeLabel('ums_settings_json') ?></strong><br />
        <pre><small><?php VarDumper::dump($model->ums_settings_json, 20, true); ?></small></pre><br />

        <strong><?php echo $model->getAttributeLabel('ums_sort_order_json') ?></strong><br />
        <pre><small><?php VarDumper::dump($model->ums_sort_order_json, 20, true); ?></small></pre>
    </div>
</div>
