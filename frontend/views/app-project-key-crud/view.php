<?php

use sales\model\appProjectKey\entity\AppProjectKey;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\appProjectKey\entity\AppProjectKey */

$this->title = $model->apk_id;
$this->params['breadcrumbs'][] = ['label' => 'App Project Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="app-project-key-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->apk_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->apk_id], [
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
                ],
                'apk_created_dt:byUserDateTime',
                'apk_updated_dt:byUserDateTime',
                'apk_created_user_id:username',
                'apk_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
