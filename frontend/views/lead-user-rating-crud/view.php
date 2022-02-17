<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserRating\entity\LeadUserRating */

$this->title = $model->lur_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead User Ratings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
    <div class="lead-user-rating-view">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="col-md-4">

            <p>
                <?= Html::a('Update', ['update', 'lur_lead_id' => $model->lur_lead_id, 'lur_user_id' => $model->lur_user_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'lur_lead_id' => $model->lur_lead_id, 'lur_user_id' => $model->lur_user_id], [
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
                    'lur_lead_id',
                    'lur_user_id:username',
                    'lur_rating',
                    'lur_created_dt:byUserDateTime',
                    'lur_updated_dt:byUserDateTime',
                ],
            ]) ?>

        </div>

    </div>

