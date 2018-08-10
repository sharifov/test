<?php

/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $contactsInfo EmployeeContactInfo[]
 * @var $contactsInfoArr EmployeeContactInfo[]
 */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use common\models\EmployeeContactInfo;
use common\models\Employee;
use common\models\Project;
use borales\extensions\phoneInput\PhoneInput;

$employeeAccess = ArrayHelper::map($model->projectEmployeeAccesses, 'project_id', 'project_id');
$availableProjects = $employeeAccess;

if ($model->role == 'admin') {
    $projectIds = ArrayHelper::map(Project::find()->asArray()->all(), 'name', 'id');
} else {
    if (Yii::$app->user->identity->role == 'supervision') {
        $availableProjects = ArrayHelper::map(Yii::$app->user->identity->projectEmployeeAccesses, 'project_id', 'project_id');
    }
    $projectIds = ArrayHelper::map(Project::find()
        ->where(['id' => $availableProjects])
        ->all(), 'name', 'id');
}

$contactsInfoArr = [];
$contactsInfo = EmployeeContactInfo::findAll([
    'employee_id' => $model->id,
    'project_id' => $projectIds
]);
foreach ($contactsInfo as $contactInfo) {
    $contactsInfoArr[$contactInfo->project_id] = $contactInfo;
}


$js = <<<JS
    $('#seller-contact-info-btn').click(function() {
        var url = $(this).data('url');
        var fields = $('#seller-contact-info input').serialize();
        $('#seller-contact-info input').each(function() {
            $(this).parent().removeClass('has-error');
        });
        $.post( url, fields, function( data ) {
            if (data.success == false) {
                $.each(data.errors, function(index, value) {
                    $('#' + index).parent().addClass('has-error');
                });
            }
        });
    });
JS;
$this->registerJs($js);

?>

<?php foreach ($projectIds as $name => $projectId) :
    $contactInfoModel = isset($contactsInfoArr[$projectId])
        ? EmployeeContactInfo::findOne(['id' => $contactsInfoArr[$projectId]->id])
        : new EmployeeContactInfo();
    $contactInfoModel->employee_id = $model->id;
    $contactInfoModel->project_id = $projectId;
    ?>
    <div class="well">
        <div class="row">
            <div class="col-sm-2">
                <?= $name ?>
                <?= Html::activeHiddenInput($contactInfoModel, '[' . $projectId . ']id') ?>
                <?= Html::activeHiddenInput($contactInfoModel, '[' . $projectId . ']employee_id') ?>
                <?= Html::activeHiddenInput($contactInfoModel, '[' . $projectId . ']project_id') ?>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <?= Html::activeTextInput($contactInfoModel, '[' . $projectId . ']email_user', [
                        'class' => 'form-control'
                    ]) ?>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <?= Html::activePasswordInput($contactInfoModel, '[' . $projectId . ']email_pass', [
                        'class' => 'form-control'
                    ]) ?>
                </div>
            </div>
            <div class="col-sm-3">
                <?= PhoneInput::widget([
                    'name' => 'EmployeeContactInfo[' . $projectId . '][direct_line]',
                    'id' => Html::getInputId($contactInfoModel, '[' . $projectId . ']direct_line'),
                    'value' => $contactInfoModel->direct_line,
                    'jsOptions' => [
                        'nationalMode' => false,
                        'preferredCountries' => false,
                        'onlyCountries' => ['us'],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<div class="form-group">
    <?= Html::button('Save', [
        'class' => 'btn btn-primary',
        'id' => 'seller-contact-info-btn',
        'data-url' => Yii::$app->urlManager->createUrl([
            'employee/seller-contact-info',
            'employeeId' => Yii::$app->user->identity->getId()
        ])
    ]) ?>
</div>

