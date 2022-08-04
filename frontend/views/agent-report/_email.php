<?php

use src\access\EmployeeProjectAccess;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\bootstrap4\Modal;
use src\entities\email\EmailInterface;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $searchModel common\models\search\AgentActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id);

?>
<?php
$js = <<<JS
$('body').on('click', '.chat__details', function () {
        let id = $(this).data('id');
        $('#object-email-view').attr('data', '/email/view?id='+id+'&preview=1');
        var popup = $('#modal-email-view');
        popup.modal('show');
        return false;
    });
JS;

$this->registerJs($js);?>

<h1><i class="fa fa-flag"></i> <?= \yii\helpers\Html::encode($title) ?></h1>
<?php Pjax::begin(['id' => 'email', /*  'enablePushState' => false,'timeout' => false */]); ?>

<?php
    $gridColumns = [
        [
            'attribute' => 'e_lead_id',
            'label' => 'Lead ID',
            'value' => static function (EmailInterface $model) {
                return $model->hasLead() ? Html::a($model->lead->id, [
                    'lead/view',
                    'gid' => $model->lead->gid
                ]) : '-';
            },
            'contentOptions' => [
                'style' => 'width:60px'
            ],
            'format' => 'raw'
        ],
        [
            'label' => 'Message',
            'value' => static function (EmailInterface $model) {
                return '<a class="chat__details" href="#" data-id="' . $model->e_id . '"><i class="fa fa-search-plus"></i> ' . $model->emailSubject . '</a>';
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'e_project_id',
            'label' => 'Project',
            'value' => static function (EmailInterface $model) {
                return $model->project->name ?? '-';
            },
            'filter' => $projectList,
            'format' => 'raw',
            ],
    ];

    ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]);

    ?>

    <?php Pjax::end(); ?>


<?php Modal::begin([
    'id' => 'modal-email-view',
    'title' => 'Email view',
    'size' => Modal::SIZE_LARGE
])?>
    <div class="view-mail">
        <object id="object-email-view" width="100%" height="800" data=""></object>
    </div>
<?php Modal::end()?>