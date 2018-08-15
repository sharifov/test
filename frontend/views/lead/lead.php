<?php
/**
 * @var $leadForm LeadForm
 */

use yii\bootstrap\Html;
use frontend\models\LeadForm;

?>

<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title"><?= Html::encode($this->title) ?></h2>
            <div class="page-header__general">
                <?php if ($leadForm->getLead()->isNewRecord) : ?>
                    <div class="page-header__general-item">
                        <strong>Lead Status:</strong>
                        <span id="status-label"><span class="label status-label label-info">New</span></span>
                    </div>
                <?php else : ?>
                    <?php if (!empty($leadForm->getLead()->employee_id)) : ?>
                        <div class="page-header__general-item">
                            <strong>Assigned to Agent: </strong>
                            <?= $leadForm->getLead()->employee->username ?>
                        </div>
                    <?php endif; ?>
                    <div class="page-header__general-item">
                        <strong>Rating:</strong>
                        <?= $this->render('partial/_rating', [
                            'lead' => $leadForm->getLead()
                        ]) ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>Client Time:</strong>
                        <?= $leadForm->getLead()->getClientTime(); ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>Lead Status:</strong>
                        <?= $leadForm->getLead()->getStatusLabel() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="main-sidebars">
    <aside class="sidebar left-sidebar sl-client-sidebar">
        <?= $this->render('partial/_client', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>

    <div class="panel panel-main">
        <?= $this->render('partial/_actions', [
            'leadForm' => $leadForm
        ]);
        ?>

        <div class="sl-request-content">
            <?= \common\widgets\Alert::widget() ?>

            <?= $this->render('partial/_flightDetails', [
                'leadForm' => $leadForm
            ]);
            ?>

            <?php if (!$leadForm->getLead()->isNewRecord && count($leadForm->getLead()->getQuotes())) {
                echo $this->render('partial/_quotes', [
                    'quotes' => array_reverse($leadForm->getLead()->getQuotes()),
                    'lead' => $leadForm->getLead()
                ]);
            } ?>

            <?php if (!$leadForm->getLead()->isNewRecord) {
                echo $this->render('partial/_notes', [
                    'notes' => $leadForm->getLead()->getNotes()
                ]);

                echo $this->render('partial/_leadLog', [
                    'logs' => $leadForm->getLead()->getLogs()
                ]);
            } ?>

        </div>
    </div>

    <aside class="sidebar right-sidebar sl-right-sidebar">
        <?= $this->render('partial/_preferences', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>
</div>
