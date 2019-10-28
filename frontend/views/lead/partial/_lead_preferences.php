<?php

use common\models\Lead;
use sales\access\LeadPreferencesAccess;
use yii\helpers\Url;
use yii\web\View;
use \yii\helpers\Html;

/**
 * @var $this View
 * @var $lead Lead
 */

$leadPreferences = $lead->leadPreferences;

$manageLeadPreferencesAccess = LeadPreferencesAccess::isUserCanManageLeadPreference($lead, Yii::$app->user->id);
?>

<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-cog"></i> Lead Preferences</h2>
		<ul class="nav navbar-right panel_toolbox" style="min-width: initial;">
            <?php if($manageLeadPreferencesAccess): ?>
            <li>
                <?=
                    Html::a('<i class="fa fa-edit fa-border yellow"></i> Edit Lead Preferences', '#', [
                        'class' => 'showModalButton',
                        'title' => 'Edit Lead Preferences',
                        'data-modal_id' => 'client-manage-info',
                        'data-content-url' => Url::to([
                            'lead-view/ajax-edit-lead-preferences-modal-content',
                            'gid' => $lead->gid
                        ])
                    ])
                ?>
            </li>
            <?php endif; ?>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
		</ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <td style="background-color: #eef3f9">Market Price</td>
                    <td style="background-color: #eef3f9">Clients Budget</td>
                    <td style="background-color: #eef3f9">Stops</td>
                    <td style="background-color: #eef3f9">Delayed Charge</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= !empty($leadPreferences->market_price) ? Html::encode($leadPreferences->market_price) : '(not set)' ?></td>
                    <td><?= !empty($leadPreferences->clients_budget) ? Html::encode($leadPreferences->clients_budget) : '(not set)' ?></td>
                    <td><?= !empty($leadPreferences->number_stops) ? Html::encode($leadPreferences->number_stops) : '(not set)' ?></td>
                    <td>
                        <?php if($lead->l_delayed_charge): ?>
                            <i class="fa fa-check green"></i>
                        <?php else: ?>
                            <i class="fa fa-remove red"></i>
						<?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <td style="background-color: #eef3f9">Notes from client</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $lead->notes_for_experts ? Html::encode($lead->notes_for_experts) : '(not set)' ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>