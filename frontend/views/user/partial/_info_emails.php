<?php

use yii\widgets\Pjax;
use yii\grid\GridView;
use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailStatus;
use common\components\grid\DateTimeColumn;

?>

<?php Pjax::begin(); ?>
<?php /*echo $this->render('_info_emails_search', ['model' => $emailSearchModel]); */ ?>
<h5>Emails Stats</h5>
<div class="well">
    <?= GridView::widget([
        'dataProvider' => $emailDataProvider,
        'filterModel' => $emailSearchModel,
        'emptyTextOptions' => [
            'class' => 'text-center'
        ],
        'columns' => [
            'e_id',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'e_project_id',
                'relation' => 'project',
            ],
            [
                'attribute' => 'e_email_from',
                'value' => 'emailFrom',
            ],
            [
                'attribute' => 'e_email_to',
                'value' => 'emailTo',
            ],
            [
                'attribute' => 'e_lead_id',
                'value' => 'lead',
                'format' => 'lead',
            ],
            [
                'attribute' => 'e_case_id',
                'value' => 'case',
                'format' => 'case',
            ],
            [
                'attribute' => 'e_type_id',
                'value' => 'typeName',
                'filter' => EmailType::getList()
            ],
            [
                'attribute' => 'e_communication_id',
                'value' => 'communicationId',
            ],
            [
                'attribute' => 'e_status_id',
                'value' => 'statusName',
                'filter' => EmailStatus::getList()
            ],
            [
                'attribute' => 'e_client_id',
                'value' => 'clientId',
                'format' => 'client'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'e_created_dt'
            ],
        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>
