<?php

use common\models\Lead;
use sales\model\leadData\services\LeadDataDictionary;
use yii\web\View;

/**
 * @var View $this
 * @var Lead $lead
 */
?>

<?php if ($lead->leadData) : ?>
    <?php foreach ($lead->leadData as $leadData) : ?>
        <table class="table table-bordered table-condensed">
            <tr>
                <td style="width: 32px; background-color: #eef3f9;">
                    <?php echo LeadDataDictionary::getKeyName($leadData->ld_field_key) ?>
                </td>
                <td style="">
                    <?php echo $leadData->ld_field_value ?>
                </td>
            </tr>
        </table>
    <?php endforeach ?>
<?php else : ?>
    <p>Lead Data not found</p>
<?php endif ?>
