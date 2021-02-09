<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLog\FileLog */

$this->title = $model->fl_id;
$this->params['breadcrumbs'][] = ['label' => 'File Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->fl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fl_id], [
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
                'fl_id',
                'fl_fs_id',
                'fl_fsh_id',
                'fl_type_id:fileLogType',
                'fl_created_dt',
                'fl_ip_address',
                'fl_user_agent',
            ],
        ]) ?>

    </div>

</div>
