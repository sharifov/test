<?php

namespace sales\helpers\call;

use common\models\Call;
use common\models\Department;
use DateTime;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\callLog\entity\callLog\CallLogType;
use yii\bootstrap4\Dropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class CallHelper
{
    /**
     * @param string $phone
     * @param bool $access
     * @param string|null $title
     * @param array $dataParams
     * @param string|null $tag
     * @return string
     */
    public static function callNumber(string $phone, bool $access, string $title = '', array $dataParams = [], ?string $tag = 'span'): string
    {
        $title = $title ?: $phone;

        $options = [
            'data-phone-number' => $phone,
            'data-confirm' => isset($dataParams['confirm']) ? 1 : 0,
            'data-call' => isset($dataParams['call']) ? 1 : 0,
            'class' => $access ? 'wg-call badge badge-pill badge-light' : ''
        ];

        if (!empty($dataParams['data-title'])) {
            $options['data-title'] = $dataParams['data-title'];
        }

        if (!empty($dataParams['phone-from-id'])) {
            $options['data-phone-from-id'] = $dataParams['phone-from-id'];
        }

        if (!empty($dataParams['data-user-id'])) {
            $options['data-user-id'] = $dataParams['data-user-id'];
        } else {
            $options['data-user-id'] = null;
        }

        $disableIcon = $dataParams['disable-icon'] ?? false;
        if ($disableIcon) {
            return Html::tag($tag, $title, $options);
        }

        $iconClass = $dataParams['icon-class'] ?? 'fa fa-phone';
        $iconTag = Html::tag('i', '', [
            'class' => $iconClass
        ]);

        return Html::tag($tag, $iconTag . ' ' . $title, $options);
    }

    /**
     * @param array $phoneNumbers
     * @param string|null $dropdownBtnContent
     * @param bool $access
     * @param array $buttonOptions
     * @return string
     * @throws \Exception
     */
    public static function callNumbersDropdownList(array $phoneNumbers, ?string $dropdownBtnContent, bool $access, $buttonOptions = []): string
    {
        $dropdownBtnContent = $dropdownBtnContent ?? '<i class="fa fa-phone"></i> Phone List';
        $dropdownBtn = Html::tag('button', $dropdownBtnContent, [
            'class' => 'btn dropdown-toggle ' . ($buttonOptions['class'] ?? 'btn-secondary'),
            'type' => 'button',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false'
        ]);

        $numbers = [];
        foreach ($phoneNumbers as $phoneNumber) {
            $numbers = [
                'label' => self::callNumber(
                    $phoneNumber['phone'] ?? '',
                    $access,
                    $phoneNumber['title'] ?? '',
                    $phoneNumbers['dataParams'] ?? []
                ),
                'encode' => false
            ];
        }

        $widget = Dropdown::widget([
            'items' => [
                $numbers
            ],
        ]);

        return Html::tag('div', $dropdownBtn . $widget, ['class' => 'dropdown']);
    }

    public static function formatCallHistoryByDate(array $callHistory, string $userTimezone): array
    {
        $result = [
            'Today' => [],
            'Yesterday' => [],
        ];

        foreach ($callHistory as $call) {
            $currentDate = new DateTime('now', new \DateTimeZone('UTC'));
            $currentDate->setTimezone(new \DateTimeZone($userTimezone));
            $currentDate->setTime(0, 0, 0);

            $callDate = new DateTime($call['cl_call_created_dt'], new \DateTimeZone('UTC'));
            $callDate->setTimezone(new \DateTimeZone($userTimezone));
            $callDate->setTime(0, 0, 0);

            $diff = $currentDate->diff($callDate);
            $diffDays = (int)$diff->format("%R%a");

            switch ($diffDays) {
                case 0:
                    $result['Today'][] = $call;
                    break;
                case -1:
                    $result['Yesterday'][] = $call;
                    break;
                default:
                    $result[date('Y-m-d', strtotime($call['cl_call_created_dt']))][] = $call;
            }
        }

        return $result;
    }

    public static function formCallToHistoryTab($call): string
    {
        $callType = (int)$call['cl_type_id'];
        $title = '';
        if ($call['user_id']) {
            $phone = $call['formatted'];
        } elseif ($callType === Call::CALL_TYPE_OUT) {
            $phone = $call['cl_phone_to'];
            $title = $call['formatted'] !== $call['cl_phone_to'] ? $call['formatted'] : '';
        } else {
            $phone = $call['cl_phone_from'];
            $title = $call['formatted'] !== $call['cl_phone_from'] ? $call['formatted'] : '';
        }

        $tpl = ' 
            <li class="calls-history__item contact-info-card">
                <div class="contact-info-card__status">
                    <div class="contact-info-card__call-icon">';
        if ($callType === CallLogType::IN && (int)$call['cl_status_id'] === CallLogStatus::NOT_ANSWERED) {
            $tpl .= '<img src="/img/pw-missed.svg">';
        } elseif ($callType === CallLogType::IN) {
            $tpl .= '<img src="/img/pw-incoming.svg">';
        } else {
            $tpl .= '
                <div class="contact-info-card__call-icon">
                    <img src="/img/pw-outgoing.svg">
                </div>';
        }
        $tpl .= '</div>
                </div>
                <div class="contact-info-card__details">
                    <div class="contact-info-card__line history-details">
                        <strong class="contact-info-card__name phone-dial-history" style="cursor:pointer;"
                                data-call-sid="' . $call['cl_call_sid'] . '"
                                data-title="' .  $title  . '"
                                data-user-id="' . $call['user_id'] . '"
                                data-phone="' . Html::encode($phone) . '"
                                data-project-id="' . Html::encode($call['cl_project_id']) . '"
                                data-department-id="' . Html::encode($call['cl_department_id']) . '"
                                data-client-id="' . Html::encode($call['cl_client_id']) . '"';
        if ((int)$call['cl_type_id'] === Call::CALL_TYPE_OUT) {
            $tpl .= ' data-source-type-id="' . $call['cl_category_id'] . '"';
            $tpl .= ' data-lead-id="' . $call['lead_id'] . '"';
            $tpl .= ' data-case-id="' . $call['case_id'] . '"';
        } elseif ((int)$call['cl_type_id'] === Call::CALL_TYPE_IN) {
            $department = (int)$call['cl_department_id'];
            $dep = Department::findOne($department);
            if ($dep && ($departmentParams = $dep->getParams())) {
                if ($departmentParams->object->type->isLead()) {
                    if ($call['lead_id']) {
                        $tpl .= ' data-source-type-id="' . Call::SOURCE_LEAD . '"';
                        $tpl .= ' data-lead-id="' . $call['lead_id'] . '"';
                    }
                } elseif ($departmentParams->object->type->isCase()) {
                    if ($call['case_id']) {
                        $tpl .= ' data-source-type-id="' . Call::SOURCE_CASE . '"';
                        $tpl .= ' data-case-id="' . $call['case_id'] . '"';
                    }
                }
            }
        }

        $tpl .= '>';
        $tpl .= Html::encode($call['formatted']);
        $tpl .= ' </strong>
                        <small class="contact-info-card__timestamp">' . $call['cl_call_created_dt'] . '</small>
                    </div>
                    <div class="contact-info-card__line history-details">
                        <span class="contact-info-card__call-type">';
        $tpl .= CallLogType::getName($callType);
        if ($call['cl_category_id']) {
            $tpl .= ' - ' . (\common\models\Call::SOURCE_LIST[$call['cl_category_id']] ?? 'undefined');
        }
        $tpl .= ' </span>
                        <small><i class="contact-info-card__call-info fa fa-info btn-history-call-info" data-call-sid="' . $call['cl_call_sid'] . '"> </i></small>
                    </div>';
        if ($call['callNote']) {
            $tpl .= '<div class="contact-info-card__line history-details">
                        <div class="contact-info-card__note">
                            <span class="contact-info-card__note-message">' . Html::encode($call['callNote']) . '</span>
                        </div>
                    </div>';
        }
        $tpl .= '</div>
            </li>';
        return $tpl;
    }

    /**
     * @param string $sec
     * @param string $delimiter
     * @return string
     */
    public static function customizedDuration(string $sec, string $delimiter = ':'): string
    {
        $seconds = $sec % 60;
        $minutes = floor($sec / 60 % 60);
        $hours   = floor($sec / 3600);

        $seconds = ($seconds > 0) ? str_pad($seconds, 2, "0", STR_PAD_LEFT) : '00';
        $minutes = ($minutes > 0) ? str_pad($minutes, 2, "0", STR_PAD_LEFT) . $delimiter : '00' . $delimiter;
        $hours   = ($hours > 0) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . $delimiter : '00' . $delimiter;

        return "$hours$minutes$seconds";
    }

    public static function displayAudioTag(string $recordingUrl, string $callSid, array $audioOptions = []): string
    {
        $defaultAudioOptions = [
            'controls' => 'controls',
            'controlslist' => 'nodownload',
            'style' => 'width: 350px; height: 25px'
        ];
        $source = Html::tag('source', null, ['src' => $recordingUrl, 'type' => 'audio/mpeg']);
        $audio = Html::tag('audio', $source, ArrayHelper::merge($defaultAudioOptions, $audioOptions));
        return Html::tag('div', $audio, ['class' => 'audio-wrapper', 'data-sid' => $callSid]);
    }

    public static function displayAudioBtn(string $recordingUrl, string $dateFormat, int $duration, string $sid, bool $isConferenceRecording = false): string
    {
        return Html::button(
            gmdate($dateFormat, $duration) . ' <i class="fa fa-volume-up"></i>',
            [
                'title' => $duration . ' (sec)',
                'class' => 'btn btn-' . ($duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url',
                'data-source_src' => $recordingUrl,
                'data-conference-recording' => $isConferenceRecording,
                'data-sid' => $sid
            ]
        );
    }
}
