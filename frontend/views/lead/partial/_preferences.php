<?php

use yii\widgets\ActiveForm;
use frontend\models\LeadForm;
use common\models\ProjectEmployeeAccess;
use common\models\ClientPhone;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $formPreferences ActiveForm
 * @var $leadForm LeadForm
 * @var $nr integer
 * @var $newPhone ClientPhone
 */

$formId = sprintf('%s-form', $leadForm->getLeadPreferences()->formName());
?>

<?php $formPreferences = ActiveForm::begin([
    'enableClientValidation' => false,
    'id' => $formId
]); ?>
<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Lead Info</h3>
    <div class="row">
        <div class="col-md-6">
            <?php if ($leadForm->getLead()->isNewRecord) : ?>
                <?= $formPreferences->field($leadForm->getLead(), 'source_id')
                    ->dropDownList(ProjectEmployeeAccess::getAllSourceByEmployee(), [
                        'prompt' => 'Select'
                    ])->label('Marketing Info:') ?>
            <?php else : ?>
                <div class="form-group field-lead-sub_source_id">
                    <label class="control-label" for="lead-sub_source_id">Marketing Info:</label><br/>
                    <?= sprintf('%s - [%s]',
                        $leadForm->getLead()->source->name,
                        $leadForm->getLead()->project->name) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if ($leadForm->getLead()->uid == null): ?>
                <?= $formPreferences->field($leadForm->getLead(), 'uid')
                    ->textInput([
                        'class' => 'form-control lead-form-input-element'
                    ]) ?>
            <?php else: ?>
                <?= $formPreferences->field($leadForm->getLead(), 'uid')
                    ->textInput(['readonly' => true]) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$leadForm->getLead()->isNewRecord && !empty($leadForm->getLead()->request_ip)) : ?>
        <div class="row">
            <div class="col-md-12">
                <?php

                $ipData = @json_decode($leadForm->getLead()->request_ip_detail, true);

                $strData[] = isset($ipData['country']) ? 'Country: <b>' . $ipData['country'] . '</b>' : '';
                $strData[] = isset($ipData['state']) ? 'State: <b>' . $ipData['state'] . '</b>' : '';
                $strData[] = isset($ipData['city']) ? 'City: <b>' . $ipData['city'] . '</b>' : '';

                $str = implode('<br> ', $strData);

                $popoverId = 'ip-popup';
                $commentTemplate = '<small>' . $str . '</small>';

                $ipCount = \common\models\Lead::find()->where([
                    'request_ip' => $leadForm->getLead()->request_ip
                ])->andWhere(['NOT IN', 'id', $leadForm->getLead()->id])->count();

                echo '<br>' . Html::a('IP address: ' . $leadForm->getLead()->request_ip . ($ipCount ? ' - ' . $ipCount . ' <i class="fa fa-clone"></i>' : ''), 'javascript:void(0);', [
                        'id' => $popoverId,
                        'data-toggle' => 'popover',
                        'data-placement' => 'bottom',
                        'data-content' => $commentTemplate,
                        'class' => 'btn sl-client-field-del client-comment-phone-button',
                    ]);

                ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<div class="sidebar__section">
    <h3 class="sidebar__subtitle">Preferences</h3>
    <div class="row">
        <div class="col-md-6">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'market_price')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
        </div>
        <div class="col-md-6">
            <?= $formPreferences->field($leadForm->getLeadPreferences(), 'clients_budget')
                ->textInput([
                    'class' => 'form-control lead-form-input-element'
                ]) ?>
        </div>
    </div>
    <?= $formPreferences->field($leadForm->getLeadPreferences(), 'number_stops')
        ->textInput([
            'class' => 'form-control lead-form-input-element',
            'type' => 'number',
            'min' => 0,
        ]) ?>
    <?= $formPreferences->field($leadForm->getLead(), 'notes_for_experts')
        ->textarea([
            'rows' => 7,
            'class' => 'form-control lead-form-input-element'
        ]) ?>
</div>
<?php ActiveForm::end(); ?>

<div class="sidebar__section" id="user-actions-block"
     style="display: <?= ($leadForm->getLead()->isNewRecord) ? 'none' : 'block' ?>;">
    <div class="btn-wrapper">
        <?= Html::button('<span class="btn-icon"><i class="fa fa-list"></i></span> View client actions', [
            'id' => 'view-client-actions-btn',
            'class' => 'btn btn-primary btn-with-icon'
        ]) ?>
    </div>
</div>