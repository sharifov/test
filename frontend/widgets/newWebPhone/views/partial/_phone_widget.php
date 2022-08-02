<?php

use src\auth\Auth;
use yii\web\View;

/** @var $showWidgetContent bool */
/** @var $userCallStatus \common\models\UserCallStatus */
/** @var View $this */
/** @var array $userPhones */
/** @var array $userEmails */
/** @var int $countMissedCalls */
/** @var array $formattedPhoneProject */

?>

<div class="phone-widget" style="margin-bottom: 30px">

  <div class="phone-widget__header">
    <div class="phone-widget__heading">
      <div class="number-toggle">
        <input type="checkbox" id="number-status3" class="call-status-switcher" <?= ($userCallStatus && $userCallStatus->isReady() ? 'checked' : '') ?>>
        <label for="number-status3" id="pw_status_name"><?= ($userCallStatus && $userCallStatus->isReady() ? 'ON' : 'OFF') ?></label>
        <div class="status-confirmation"></div>
      </div>
        <i class="fa fa-exclamation-triangle phone-widget-heading__warning" data-toggle="popover" data-content="" data-placement="bottom"></i>
        <ul class="call-filter">
            <li class="call-filter__alert is-visible"><a class="call-filter__toggle call-filter__toggle--line-active" title="Active Calls" data-call-filter="active">0</a></li>
            <li class="call-filter__alert is-visible"><a class="call-filter__toggle call-filter__toggle--line-direct" title="Direct Line" data-call-filter="direct">0</a></li>
            <li class="call-filter__alert is-visible"><a class="call-filter__toggle call-filter__toggle--line-general" title="General Line" data-call-filter="general">0</a></li>
        </ul>

        <div id="widget-phone-notifications"></div>

        <?php
        /*
        <!-- phone notifications -->
            <div class="phone-notifications phone-notifications--general-calls">
                <ul class="phone-notifications__list">
                    <li class="phone-notifications__item phone-notifications__item--shown">
                        <div class="incoming-notification">
                            <i class="user-icn">G</i>
                            <div class="incoming-notification__inner">
                                <div class="incoming-notification__info">
                                    <div class="incoming-notification__general-info">
                                        <b class="incoming-notification__name">Geffy Morgan Jefferson</b>
                                        <span class="incoming-notification__phone">+1 (888) 88 888 88</span>
                                        <div class="incoming-notification__project">
                                            <b class="incoming-notification__project-name">WOWFARE</b>
                                            <i>•</i>
                                            <span class="incoming-notification__position">Sales General</span>
                                        </div>
                                    </div>
                                    <div class="incoming-notification__timer">
                                        <span>24:32</span>
                                    </div>
                                </div>
                                <div class="incoming-notification__action-list">
                                    <div class="incoming-notification__dynamic">
                                        <a href="#" class="incoming-notification__action incoming-notification__action--line">
                                            <i class="fa fa-random"></i>
                                        </a>

                                        <a href="#" class="incoming-notification__action incoming-notification__action--info">
                                            <i class="fa fa-info"></i>
                                        </a>

                                        <a href="#" class="incoming-notification__action incoming-notification__action--phone">
                                            <i class="fa fa-phone"></i>
                                        </a>
                                    </div>
                                    <a href="#" class="incoming-notification__action incoming-notification__action--min">
                                        <i class="fa fa-long-arrow-alt-down"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        <!-- end phone notifications -->
        */
        ?>

        <!-- <a href="#" class="phone-widget__dev header-action-small toggle-bar-logs"><i class="fas fa-tools"></i></a> -->
      <!-- <span class="phone-widget__title">Calls</span> -->
      <a class="phone-widget__settings header-action-small toggle-bar-settings"><i class="fa fa-cog"></i></a>
      <a class="phone-widget__close header-action-small">
        <!-- <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z"
            fill="white" />
        </svg> -->
        <i class="fa fa-times"></i>
      </a>
    </div>
    <ul class="phone-widget__header-actions">
      <li>
        <a data-toggle-tab="tab-phone" class="is_active" data-call-in-progress="false">
          <i class="fas fa-phone"></i>
          <span>Call</span>
        </a>
      </li>
        <?php if (Auth::can('PhoneWidget_HistoryTab')) : ?>
            <li>
                <a data-toggle-tab="tab-history" data-missed-calls="0" style="margin-right: 15px">
                    <i class="fas fa-file-invoice"></i>
                    <span>history</span>
                </a>
            </li>
        <?php endif;?>
        <?php if (Auth::can('PhoneWidget_ContactsTab')) : ?>
              <li>
                <a data-toggle-tab="tab-contacts" >
                  <i class="far fa-address-book"></i>
                  <span>Contacts</span>
                </a>
              </li>
        <?php endif;?>
   
    </ul>
  </div>
  <div class="phone-widget__body">
    <?= $this->render('tab/call', [
      'userCallStatus' => $userCallStatus,
      'countMissedCalls' => $countMissedCalls,
      'formattedPhoneProject' => $formattedPhoneProject
    ]); ?>
    <?= $this->render('tab/contacts', ['userPhones' => $userPhones, 'userEmails' => $userEmails]); ?>
    <?= $this->render('tab/history'); ?>
    <div class="widget-phone__contact-info-modal widget-modal contact-modal-info"></div>
    <div class="widget-phone__messages-modal widget-modal messages-modal"></div>
    <div class="widget-phone__email-modal widget-modal email-modal"></div>

    <div class="widget-line-overlay" style="display:none">
      <div class="widget-line-overlay__header">
          <div class="widget-line-overlay__queue-marker">
              <span data-queue-marker>Calls Queue</span>
              <a class="widget-line-overlay__show-all-queues" data-call-filter="all" >All Calls</a>
          </div>
      </div>
      <div class="widget-line-overlay__body scrollable-block">
          <ul class="queue-separator" id="queue-separator"> </ul>
      </div>
    </div>

        <?php /*
        <div class="widget-phone__contact-info-modal widget-modal contact-modal-info">
            <a href="#" class="widget-modal__close">
                <i class="fa fa-arrow-left"></i>
                Back to contacts</i>

            </a>
            <div class="contact-modal-info__user">
                <div class="agent-text-avatar">
      <span>
        A
      </span>
                </div>
                <h3 class="contact-modal-info__name">Geordan Reyney</h3>

                <div class="contact-modal-info__actions">
                    <ul class="contact-options-list">
                        <li class="contact-options-list__option js-edit-mode">
                            <i class="fa fa-user"></i>
                            <span>EDIT</span>
                        </li>
                        <li class="contact-options-list__option js-trigger-messages-modal">
                            <i class="fa fa-comment-alt"></i>
                            <span>SMS</span>
                        </li>

                        <li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">
                            <i class="fa fa-phone"></i>
                            <span>Call</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="contact-modal-info__body">
                <ul class="contact-modal-info__contacts contact-full-info">
                    <li>
                        <div class="form-group">
                            <label for="">Type</label>
                            <div class="form-control-wrap" data-type="company">

                                <i class="fa fa-building contact-type-company"></i>
                                <i class="fa fa-user contact-type-person"></i>
                                <select readonly type="text"
                                        class="form-control select-contact-type" value="Company"
                                        autocomplete="off" readonly disabled>
                                    <option value="company">Company</option>
                                    <option value="person">Person</option>
                                </select>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="form-group">
                            <label for="">Phone 1</label>
                            <input readonly type="text" class="form-control"
                                   value="+373-69-223344" autocomplete="off">
                        </div>
                        <ul class="actions-list">
                            <li class="actions-list__option actions-list__option--phone js-call-tab-trigger">
                                <i class="fa fa-phone"></i>
                            </li>
                            <li class="actions-list__option js-trigger-messages-modal">
                                <i class="fa fa-comment-alt"></i>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="form-group">
                            <label for="">Phone 2</label>
                            <input readonly type="text" class="form-control"
                                   value="+1-222-555-8888" autocomplete="off">
                        </div>

                        <ul class="actions-list">
                            <li class="actions-list__option actions-list__option--phone js-call-tab-trigger">
                                <i class="fa fa-phone"></i>
                            </li>
                            <li class="actions-list__option js-trigger-messages-modal">
                                <i class="fa fa-comment-alt"></i>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input readonly type="email" class="form-control"
                                   value="andrew.johnson@gttglobal.com" autocomplete="off">
                        </div>
                        <ul class="actions-list">
                            <li class="actions-list__option js-trigger-email-modal">
                                <i class="fa fa-envelope "></i>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="form-group">
                            <label for="">Post</label>
                            <input readonly type="text" class="form-control"
                                   value="Head of Ticketing Department" autocomplete="off">
                        </div>
                    </li>
                    <li>
                        <div class="form-group">
                            <label for="">Date of birth</label>
                            <input readonly type="text" class="form-control" value="24-03-1972"
                                   autocomplete="off">
                        </div>
                    </li>
                </ul>

                <a href="#" class="contact-modal-info__remove-contact">DELETE CONTACT</a>
            </div>

        </div>
        <div class="widget-phone__messages-modal widget-modal messages-modal">
            <a href="#" class="widget-modal__close">
                <i class="fa fa-arrow-left"></i>
                Back to contacts</i>
            </a>
            <div class="modal-messaging__contact-info">
                <div class="modal-messaging__info-list">
                    <div class="modal-messaging__info-item">SMS to <span
                                class="modal-messaging__contact-name">Amelie Chu</span>
                    </div>
                    <span class="modal-messaging__info-number">+1-222-888-5555</span>

                </div>
            </div>


            <div class="messages-modal__messages-scroll">
                <div class="messages-modal__body">
                    <span class="section-separator">Yestrday, 12 Mar 2020</span>
                    <ul class="messages-modal__msg-list">
                        <li class="messages-modal__msg-item pw-msg-item">
                            <div class="pw-msg-item__avatar">
                                <div class="agent-text-avatar">
              <span>
                B
              </span>
                                </div>
                            </div>
                            <div class="pw-msg-item__msg-main">

                                <div class="pw-msg-item__data">
                                    <span class="pw-msg-item__name">Amelie Chu</span>
                                    <span class="pw-msg-item__timestamp">11:14 PM</span>
                                </div>
                                <div class="pw-msg-item__msg-wrap">
                                    <p class="pw-msg-item__msg">Need your urgent advice.</p>
                                </div>
                            </div>
                        </li>
                        <li class="messages-modal__msg-item pw-msg-item pw-msg-item--user">
                            <div class="pw-msg-item__avatar">
                                <div class="agent-text-avatar">
              <span>
                B
              </span>
                                </div>
                            </div>
                            <div class="pw-msg-item__msg-main">

                                <div class="pw-msg-item__data">
                                    <span class="pw-msg-item__name">Me</span>
                                    <span class="pw-msg-item__timestamp">11:14 PM</span>
                                </div>
                                <div class="pw-msg-item__msg-wrap">
                                    <p class="pw-msg-item__msg">Of course, Amelie. What’s the
                                        problem?</p>
                                </div>
                            </div>

                        </li>
                        <li class="messages-modal__msg-item pw-msg-item pw-msg-item--user">
                            <div class="pw-msg-item__avatar">
                                <div class="agent-text-avatar">
              <span>
                B
              </span>
                                </div>
                            </div>
                            <div class="pw-msg-item__msg-main">

                                <div class="pw-msg-item__data">
                                    <span class="pw-msg-item__name">Me</span>
                                    <span class="pw-msg-item__timestamp">11:14 PM</span>
                                </div>
                                <div class="pw-msg-item__msg-wrap">
                                    <p class="pw-msg-item__msg">Of course, Amelie. What’s the
                                        problem?</p>
                                </div>
                            </div>

                        </li>
                        <li class="messages-modal__msg-item pw-msg-item">
                            <div class="pw-msg-item__avatar">
                                <div class="agent-text-avatar">
              <span>
                B
              </span>
                                </div>
                            </div>
                            <div class="pw-msg-item__msg-main">

                                <div class="pw-msg-item__data">
                                    <span class="pw-msg-item__name">Amelie Chu</span>
                                    <span class="pw-msg-item__timestamp">11:14 PM</span>
                                </div>
                                <div class="pw-msg-item__msg-wrap">
                                    <p class="pw-msg-item__msg">Need your urgent advice.</p>
                                </div>
                            </div>
                        </li>
                        <li class="messages-modal__msg-item pw-msg-item">
                            <div class="pw-msg-item__avatar">
                                <div class="agent-text-avatar">
              <span>
                B
              </span>
                                </div>
                            </div>
                            <div class="pw-msg-item__msg-main">

                                <div class="pw-msg-item__data">
                                    <span class="pw-msg-item__name">Amelie Chu</span>
                                    <span class="pw-msg-item__timestamp">11:14 PM</span>
                                </div>
                                <div class="pw-msg-item__msg-wrap">
                                    <p class="pw-msg-item__msg">Need your urgent advice.</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="messages-modal__footer">
                <div class="messages-modal__input-group">
                    <input type="text" class="messages-modal__msg-input"
                           placeholder="Your Message">
                    <button class="messages-modal__send-btn">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>
        <div class="widget-phone__email-modal widget-modal email-modal">
            <a href="#" class="widget-modal__close">
                <i class="fa fa-arrow-left"></i>
                Back to contacts</i>
            </a>
            <div class="modal-messaging__contact-info">
                <div class="modal-messaging__info-list">
                    <div class="modal-messaging__info-item">Email to <span
                                class="modal-messaging__contact-name">Amelie Chu</span>
                    </div>
                    <span class="modal-messaging__info-number">amelie.chu@gttglobal.com</span>

                </div>
            </div>


            <div class="email-modal__messages-scroll">
                <div class="email-modal__body">
                    <div class="email-modal__input-group">
                        <div class="email-modal__subject-block">
                            <div class="email-modal__modal-input-list">
                                <input type="text" class="email-modal__contact-input"
                                       placeholder="Subject">
                            </div>
                            <ul class="subject-option">
                                <li class="subject-option__add" data-add-type="cc">Add CC</li>
                                <li class="subject-option__add" data-add-type="bcc">Add BCC</li>
                            </ul>
                        </div>
                        <textarea class="email-modal__msg-input" placeholder="Your Message"
                                  name="" id="" cols="30" rows="10"></textarea>

                    </div>
                    <button class="email-modal__send-btn">
        <span>
          SEND
        </span>
                        <i class="fa fa-paper-plane"></i>
                    </button>
                    <a href="#" class="email-history" target="_blank">View Email History with
                        Amelie</a>
                </div>

            </div>
        </div>
        */ ?>

  </div>


  <!-- <div class="phone-widget__additional-bar additional-bar" id="bar-logs">
    <div class="additional-bar__header">
      <span class="additional-bar__header-title">
        Logs
      </span>
      <a href="#" class="additional-bar__close">
        <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="additional-bar__body">
      
    </div>
  </div> -->

  <div class="phone-widget__additional-bar additional-bar" id="bar-settings">
    <div class="additional-bar__header">
      <span class="additional-bar__header-title">
        Settings
      </span>
      <a class="additional-bar__close">
        <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="additional-bar__body tabs">
        <ul class="tabs__nav tab-nav">
            <li class="wp-tab-device"><a href="#tab-device" class="tab-trigger active-tab">Devices</a></li>
            <li><a href="#tab-device-status" class="tab-trigger">Statuses</a></li>
            <li><a href="#tab-logs" class="tab-trigger wp-devices-tab-log">Logs</a></li>
        </ul>
        <ul class="tabs__container tab-container">
            <li class="tabs__item" id="tab-device">
                <div id="output-selection">
                    <label>Microphone Devices</label>
                    <select id="microphone-devices" class="ringtone-devices form-control" multiple></select>
                    <label>Speaker Devices</label>
                    <select id="speaker-devices" class="speaker-devices form-control" multiple></select><br/>
                </div>
            </li>
            <li class="tabs__item" id="tab-device-status">
                <span class="tab-device-status"></span><br>
                <span class="tab-device-status-twilio"></span><br>
                <span class="tab-device-status-speaker"></span><br>
                <span class="tab-device-status-microphone"></span><br>
            </li>
            <li class="tabs__item" id="tab-logs">
                <pre class="logs-block">
                </pre>
            </li>
        </ul>
       

        

    </div>

  </div>


</div>