<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ApiUser */

$this->title = 'ApiUser: ' . $model->au_id;
$this->params['breadcrumbs'][] = ['label' => 'Api Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card card-default">
        <div class="card-body">
            <div class="panel-body panel-collapse collapse show">
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->au_id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->au_id], [
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
                        'au_id',
                        'au_name',
                        'au_api_username',
                        'au_api_password',
                        'au_email:email',
                        'au_enabled:boolean',
                        'au_updated_dt',
                        'au_rate_limit_number',
                        'au_rate_limit_reset',
                        //'auUpdatedUser.username',
                        'auProject.name:projectName',
                    ],
                ]) ?>
            </div>
        </div>
    </div>

</div>
