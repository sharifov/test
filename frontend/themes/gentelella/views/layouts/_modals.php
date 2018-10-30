<?php
/* @var $this \yii\web\View */

?>

<div class="modal modal-quote fade" id="get-request-flow-transition" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Lead - Flow Transition
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal modal-quote fade" id="get-quote-status-log" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Quote - Status Log
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal modal-events fade" id="log-events" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Client Request Actions
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- MODAL ERROR WINDOWS -->
<div class="modal modal-danger fade in" id="modal-error" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">Attention!</h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<div class="modal modal-quote fade" id="quick-search" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Quick search quotes
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal modal-quote fade" id="create-quote" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add quote!</h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div id="preloader" class="overlay hidden">
    <div class="preloader">
        <span class="fa fa-spinner fa-pulse fa-3x fa-fw"></span>
        <div class="preloader__text">Loading...</div>
    </div>
</div>
