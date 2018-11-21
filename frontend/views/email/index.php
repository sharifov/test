<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Emails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Email', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'e_id',
            'e_reply_id',
            'e_lead_id',
            'e_project_id',
            'e_email_from:email',
            //'e_email_to:email',
            //'e_email_cc:email',
            //'e_email_bc:email',
            //'e_email_subject:email',
            //'e_email_body_html:ntext',
            //'e_email_body_text:ntext',
            //'e_attach',
            //'e_email_data:ntext',
            //'e_type_id',
            //'e_template_type_id',
            //'e_language_id',
            //'e_communication_id',
            //'e_is_deleted',
            //'e_is_new',
            //'e_delay',
            //'e_priority',
            //'e_status_id',
            //'e_status_done_dt',
            //'e_read_dt',
            //'e_error_message',
            //'e_created_user_id',
            //'e_updated_user_id',
            //'e_created_dt',
            //'e_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
