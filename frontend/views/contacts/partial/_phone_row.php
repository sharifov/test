<?php

use common\models\ClientPhone;
use common\models\Employee;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ClientPhone $phone
 */
/** @var Employee $user */
$user = Yii::$app->user->identity;
?>

<tr class="phone_row_<?php echo $phone->id ?>">
    <td title="<?= $phone::getPhoneType($phone->type) ?>" class="text-center" style="width:35px; background-color: #eef3f9; padding: 5px 10px;">
        <?= $phone::getPhoneTypeIcon($phone->type) ?>
    </td>
    <td style="padding: 5px 10px;">
        <span style="line-height: 0;" class="<?= $phone::getPhoneTypeTextDecoration($phone->type) ?>"><?= Html::encode($phone->phone) ?></span>
    </td>

    <?php if ($user->isAdmin() || $user->isSuperAdmin()): ?>
    <td class="text-right" style="width: 40px; padding: 5px 10px;">
        <a class="showModalButton" title="Edit Phone" data-content-url="<?= Url::to(
            [
                'contacts/ajax-edit-contact-phone-modal-content',
                'phone_id' => $phone->id,
            ])
        ?>" data-modal_id="sm">
            <i class="fa fa-edit text-warning"></i>
        </a>
    </td>
    <?php endif ?>
</tr>


