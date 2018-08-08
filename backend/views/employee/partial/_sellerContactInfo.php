<?php

/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $contactsInfo SellerContactInfo[]
 * @var $contactsInfoArr SellerContactInfo[]
 */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use common\models\SellerContactInfo;
use common\models\SourcePermission;
use common\models\Employee;
use common\models\Source;
use borales\extensions\phoneInput\PhoneInput;

if (in_array('admin', $model->getRoles())) {
    $projectIds = ArrayHelper::map(Source::find()->asArray()->all(), 'name', 'id');
} else {
    $permissions = SourcePermission::getPermissionByTeam(SourcePermission::TEAM_NAME_SELLER);
    $permissions[] = SourcePermission::SP_PROJECT_ADMIN;

    $projectIds = ArrayHelper::map(SourcePermission::find()->where([
        'user_id' => $model->id,
        'item_name' => $permissions
    ])->asArray()->all(), 'source_id', 'source_id');

    $projectIds = ArrayHelper::map(Source::find()->where([
        'id' => $projectIds
    ])->asArray()->all(), 'name', 'id');
}

$contactsInfoArr = [];
$contactsInfo = SellerContactInfo::findAll([
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

<div class="panel panel-default">
    <div class="panel-heading collapsing-heading">
        <?= Html::a('Seller Contact Info <i class="collapsing-heading__arrow"></i>', '#seller-contact-info', [
            'data-toggle' => 'collapse',
            'class' => 'collapsing-heading__collapse-link'
        ]) ?>
    </div>
    <div class="panel-body panel-collapse collapse in" id="seller-contact-info">
        <?php foreach ($projectIds as $name => $projectId) :
            $contactInfoModel = isset($contactsInfoArr[$projectId])
                ? SellerContactInfo::findOne(['id' => $contactsInfoArr[$projectId]->id])
                : new SellerContactInfo();
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
                            'name' => 'SellerContactInfo[' . $projectId . '][direct_line]',
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
    </div>
</div>
