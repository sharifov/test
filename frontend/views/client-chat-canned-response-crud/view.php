<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse */

$this->title = $model->cr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Canned Responses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-canned-response-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cr_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cr_id], [
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
                'cr_id',
                'cr_project_id',
                'cr_category_id',
                'cr_language_id',
                'cr_user_id',
                'cr_sort_order',
                'cr_message',
                'cr_created_dt',
                'cr_updated_dt',
            ],
        ]) ?>

    </div>

</div>
