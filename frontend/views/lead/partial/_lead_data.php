<?php

/**
 * @var $this \yii\web\View
 * @var \common\models\Lead $lead
 */

use sales\model\leadData\abac\dto\LeadDataAbacDto;
use sales\model\leadData\abac\LeadDataAbacObject;
use yii\helpers\Html;

$leadDatas = [];
foreach ($lead->leadData as $leadData) {
    $leadDataAbacDto = new LeadDataAbacDto($leadData);
    /** @abac new $leadDataAbacDto, LeadDataAbacObject::UI_INFO, LeadDataAbacObject::ACTION_READ, Show Lead Data on Lead view*/
    if (Yii::$app->abac->can($leadDataAbacDto, LeadDataAbacObject::UI_INFO, LeadDataAbacObject::ACTION_READ)) {
        $leadDatas[] = $leadData;
    }
}
?>

<?php if ($leadDatas) : ?>
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-th-list"></i> Lead Data (<?php echo count($leadDatas) ?>)</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                </li>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none;">
            <table class="table table-neutral table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Data</th>
                    <th>Created</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($leadDatas as $leadData) : ?>
                    <tr>
                        <td>
                            <?php echo Html::encode($leadData->leadDataKey->ldk_name) ?>
                        </td>
                        <td>
                            <?php echo Html::encode($leadData->ld_field_value) ?>
                        </td>
                        <td>
                            <i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($leadData->ld_created_dt)) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>

