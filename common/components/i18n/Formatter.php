<?php

namespace common\components\i18n;

use common\components\purifier\Purifier;
use common\models\Call;
use common\models\CaseSale;
use common\models\ConferenceParticipant;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\PaymentMethod;
use common\models\Project;
use common\models\Quote;
use modules\fileStorage\src\entity\fileLog\FileLogType;
use modules\fileStorage\src\entity\fileStorage\FileStorageStatus;
use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceStatus;
use modules\invoice\src\entities\invoice\InvoiceStatusAction;
use modules\invoice\src\helpers\formatters\InvoiceFormatter;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferStatus;
use modules\offer\src\entities\offer\OfferStatusAction;
use modules\offer\src\entities\offerSendLog\OfferSendLogType;
use modules\offer\src\helpers\formatters\OfferFormatter;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\OrderStatusAction;
use modules\order\src\helpers\formatters\OrderFormatter;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productOption\ProductOptionPriceType;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatusAction;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\product\src\helpers\formatters\ProductFormatter;
use modules\product\src\helpers\formatters\ProductQuoteFormatter;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\QaTaskCreateType;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRating;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\helpers\formatters\QaTaskFormatter;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use modules\shiftSchedule\src\entities\shift\Shift;
use src\entities\cases\Cases;
use src\entities\cases\CasesSourceType;
use src\helpers\PhoneFormatter;
use src\model\callLog\entity\callLog\CallLogStatus;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\client\notifications\client\ClientNotificationCommunicationType;
use src\model\client\notifications\client\ClientNotificationType;
use src\model\clientChat\entity\ClientChat;
use src\model\coupon\entity\coupon\CouponStatus;
use src\model\coupon\entity\coupon\CouponType;
use src\model\emailList\entity\EmailList;
use src\model\emailList\helpers\formatters\EmailListFormatter;
use src\model\phoneList\entity\PhoneList;
use src\model\phoneList\helpers\formatters\PhoneListFormatter;
use src\model\user\entity\paymentCategory\UserPaymentCategory;
use src\model\user\entity\payroll\UserPayroll;
use yii\helpers\FormatConverter;
use src\helpers\app\AppHelper;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class Formatter extends \yii\i18n\Formatter
{
    public function asPurify($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return Purifier::purify($value);
    }

    public function asNtextWithPurify($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $value = $this->asNtext($value);

        return Purifier::purify($value);
    }

    public function asTextWithLinks($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return Purifier::purify($value);
    }

    public function asPhoneList($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        /** @var PhoneList $value */
        return PhoneListFormatter::asFormat($value);
    }

    public function asEmailList($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        /** @var EmailList $value */
        return EmailListFormatter::asFormat($value);
    }

    public function asCallLog($logId): string
    {
        if ($logId === null) {
            return $this->nullDisplay;
        }
        return \yii\helpers\Html::a('log: ' . $logId, ['/call-log/view', 'id' => $logId]);
    }

    public function asCallLogStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return CallLogStatus::asFormat($value);
    }

    public function asCallLogCategory($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return  Call::SOURCE_LIST[$value] ?? '-';
    }

    public function asCallLogType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return CallLogType::asFormat($value);
    }

    public function asQaTaskRating($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return QaTaskRating::asFormat($value);
    }

    public function asCasesSourceType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return CasesSourceType::asFormat($value);
    }

    public function asSource(?int $id): string
    {
        if ($id === null) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-arrow-right'])
            . ' ' .
            Html::a(
                $id,
                ['/sources/view', 'id' => $id],
                ['target' => '_blank', 'data-pjax' => 0]
            );
    }

    public function asQaTask(?QaTask $task): string
    {
        if ($task === null) {
            return $this->nullDisplay;
        }

        return QaTaskFormatter::asQaTask($task);
    }

    public function asQaTaskAction($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return QaTaskActions::asFormat($value);
    }

    public function asQaTaskStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return QaTaskStatus::asFormat($value);
    }

    public function asQaTaskCreatedType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return QaTaskCreateType::asFormat($value);
    }

    public function asQaTaskObjectType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return QaTaskObjectType::asFormat($value);
    }

    public function asOfferSendLogType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OfferSendLogType::asFormat($value);
    }

    public function asInvoice(?Invoice $invoice): string
    {
        if ($invoice === null) {
            return $this->nullDisplay;
        }

        return InvoiceFormatter::asInvoice($invoice);
    }

    public function asInvoiceStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return InvoiceStatus::asFormat($value);
    }

    public function asInvoiceStatusAction($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return InvoiceStatusAction::asFormat($value);
    }

    public function asOrder(?Order $order): string
    {
        if ($order === null) {
            return $this->nullDisplay;
        }

        return OrderFormatter::asOrder($order);
    }

    public function asOrderId(?int $id): string
    {
        if ($id === null) {
            return $this->nullDisplay;
        }

        return OrderFormatter::asOrderId($id);
    }

    public function asOrderPayStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OrderPayStatus::asFormat($value);
    }

    public function asOrderStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OrderStatus::asFormat($value);
    }

    public function asOrderStatusAction($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OrderStatusAction::asFormat($value);
    }

    public function asProductQuoteStatusAction($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteStatusAction::asFormat($value);
    }

    public function asOfferStatusAction($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OfferStatusAction::asFormat($value);
    }

    public function asOffer(?Offer $offer): string
    {
        if ($offer === null) {
            return $this->nullDisplay;
        }

        return OfferFormatter::asOffer($offer);
    }

    public function asOfferStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OfferStatus::asFormat($value);
    }

    public function asProductQuoteOptionStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteOptionStatus::asFormat($value);
    }

    public function asProductOptionPriceType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductOptionPriceType::asFormat($value);
    }

    public function asProductQuoteStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteStatus::asFormat($value);
    }

    public function asProductQuote(?ProductQuote $productQuote): string
    {
        if ($productQuote === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteFormatter::asProductQuote($productQuote);
    }

    public function asPayroll($userPayrollId): string
    {
        if ($userPayrollId === null) {
            return $this->nullDisplay;
        }
        return Html::a(
            $userPayrollId,
            ['/user-payroll-crud/view', 'id' => $userPayrollId],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }

    public function asProduct(?Product $product): string
    {
        if ($product === null) {
            return $this->nullDisplay;
        }

        return ProductFormatter::asProduct($product);
    }

    public function asLead(?Lead $lead, ?string $class = null): string
    {
        if ($lead === null) {
            return $this->nullDisplay;
        }

        return \modules\lead\src\helpers\formatters\lead\Formatter::asLead($lead, $class);
    }

    public function asCase(?Cases $case, ?string $class = null): string
    {
        if ($case === null) {
            return $this->nullDisplay;
        }

        return \src\model\cases\helpers\formatters\cases\Formatter::asCase($case, $class);
    }

    public function asCaseSale(?CaseSale $caseSale): string
    {
        if ($caseSale === null) {
            return $this->nullDisplay;
        }

        return Html::tag('i', '', ['class' => 'fa fa-arrow-right'])
            . ' '
            . Html::a(
                'Case Sale',
                ['/case-sale/view', 'css_cs_id' => $caseSale->css_cs_id, 'css_sale_id' => $caseSale->css_sale_id],
                ['target' => '_blank', 'data-pjax' => 0]
            );
    }

    public function asProductType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \modules\product\src\helpers\formatters\ProductTypeFormatter::asProductType($value);
    }

    public function asPaymentMethod($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $paymentMethod = PaymentMethod::findOne($value);

        if ($paymentMethod) {
            return $paymentMethod->pm_name;
        }

        return $this->nullDisplay;
    }

    public function asQuoteType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        switch ($value) {
            case Quote::TYPE_BASE:
                $class = 'label label-info';
                break;
            case Quote::TYPE_ORIGINAL:
                $class = 'label label-success';
                break;
            case Quote::TYPE_ALTERNATIVE:
                $class = 'label label-warning';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', Quote::getTypeName($value), ['class' => $class]);
    }

    /**
     * @param $categoryId
     * @return string|null
     */
    public function asUserPaymentCategoryName($categoryId): ?string
    {
        $category = UserPaymentCategory::findOne(['upc_id' => $categoryId]);

        if ($category) {
            return $category->upc_name ?? '--';
        }
        return '--';
    }

    public function asTimer(string $dateTime, string $timerSpanClass = 'label label-info')
    {
        if (!$dateTime) {
            return $this->nullDisplay;
        }

        $currentTime = time();
        $dateTime = strtotime($dateTime);

        $diff = abs($currentTime - $dateTime);

        $icon = Html::tag('i', '', ['class' => 'fa fa-clock-o']);
        $timerSpan = Html::tag('span', '', ['class' => 'enable-timer ' . $timerSpanClass, 'data-seconds' => $diff]);
        return Html::tag('div', $icon . ' ' . $timerSpan);
    }

    /**
     * @param $statusId
     * @return string|null
     */
    public function asUserPaymentStatusName($statusId): ?string
    {
        $statusName = UserPaymentCategory::getStatusName($statusId);

        if ($statusName) {
            return $statusName;
        }
        return '--';
    }

    /**
     * @param $dateTime
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asDateTimeByUserTimezone($dateTime, string $timezone = 'UTC', string $format = 'php:d-M-Y [H:i]'): string
    {
        if (!$dateTime) {
            return $this->nullDisplay;
        }

        if (is_numeric($dateTime) && $dateTime >= 0) {
            if (strncmp($format, 'php:', 4) === 0) {
                $format = substr($format, 4);
            } else {
                $format = FormatConverter::convertDateIcuToPhp($format, "datetime", $this->locale);
            }
            try {
                $dateTime = (new \DateTimeImmutable(date('Y-m-d H:i:s', $dateTime), new \DateTimeZone('UTC')))
                ->setTimezone(new \DateTimeZone($timezone))
                ->format($format);
            } catch (\Throwable $throwable) {
                $message = AppHelper::throwableLog($throwable);
                $message['dateTime'] = $dateTime;
                $message['timezone'] = $timezone;
                $message['format'] = $format;
                $dateTime = '';
                \Yii::warning($message, 'Formatter:asByUserDateTimeSecond');
            }
        }
        return $dateTime;
    }

    /**
     * @param $dateTime
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asByUserDateTime($dateTime, $format = 'php:d-M-Y [H:i]'): string
    {
        if (!$dateTime) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' . $this->asDatetime(strtotime($dateTime), $format);
    }

    public function asByUserDate($dateTime, $format = 'php:d-M-Y'): string
    {
        if (!$dateTime) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' . $this->asDate(strtotime($dateTime), $format);
    }

    /**
     * @param $dateTime
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asByUserDateTimeWithSeconds($dateTime): string
    {
        if (!$dateTime) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' . $this->asDatetime(strtotime($dateTime), 'php:d-M-Y [H:i:s]');
    }

    /**
     * @param Employee|int|string|null $value
     * @return string
     */
    public function asUserName($value): string
    {
        if (!$value) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            $name = $value;
        } elseif ($value instanceof Employee) {
            $name = $value->username;
        } elseif (is_int($value)) {
            if ($entity = Employee::find()->select(['username'])->where(['id' => $value])->cache(3600)->one()) {
                $name = $entity->username;
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('user must be Employee|int|string|null');
        }

        return Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($name);
    }

    /**
     * @param Employee|int|string|null $value
     * @return string
     */
    public function asUserNameWithId($value): string
    {
        if (!$value) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            if ($entity = Employee::find()->select(['username', 'id'])->where(['username' => $value])->cache(3600)->one()) {
                $name = $entity->username . ' (' . $entity->id . ')';
            } else {
                $name = $value;
            }
        } elseif ($value instanceof Employee) {
            $name = $value->username . ' (' . $value->id . ')';
        } elseif (is_int($value)) {
            if ($entity = Employee::find()->select(['username', 'id'])->where(['id' => $value])->cache(3600)->one()) {
                $name = $entity->username . ' (' . $entity->id . ')';
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('user must be Employee|int|string|null');
        }

        return Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($name);
    }

    /**
     * @param $numberOfMonth
     * @return string
     */
    public function asMonthNameByMonthNumber($numberOfMonth)
    {
        return \DateTime::createFromFormat('!m', $numberOfMonth)->format('F');
    }

    public function asUserPayrollAgentStatusName($agentStatusId): ?string
    {
        return UserPayroll::getAgentStatusName($agentStatusId);
    }

    public function asUserPayrollStatusName($statusId): ?string
    {
        return UserPayroll::getStatusName($statusId);
    }

    /**
     * @param Department|int|string|null $value
     * @return string
     */
    public function asDepartmentName($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            $name = $value;
        } elseif ($value instanceof Department) {
            $name = $value->dep_name;
        } elseif (is_int($value)) {
            if ($entity = Department::findOne($value)) {
                $name = $entity->dep_name;
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('value must be Department|int|string|null');
        }

        return Html::encode($name);
    }

    public function asDepartment($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return Department::asFormat($value);
    }

    /**
     * @param Project|int|string|null $value
     * @return string
     */
    public function asProjectName($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            $name = $value;
        } elseif ($value instanceof Project) {
            $name = $value->name;
        } elseif (is_int($value)) {
            if ($entity = Project::findOne($value)) {
                $name = $entity->name;
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('value must be Project|int|string|null');
        }

        return Html::tag('span', Html::encode($name), ['class' => 'badge badge-info', 'data-toggle' => 'tooltip',  'data-original-title' => 'Project']);
    }

    public function asProjectNames(?array $projectIds, string $separator = ' ', ?string $tag = 'span'): string
    {
        if (empty($projectIds)) {
            $result = '';
        } else {
            $nameList = [];
            foreach ($projectIds as $projectId) {
                $name = $this->asProjectName($projectId);
                $nameList[] = $tag ? Html::tag($tag, $name) : $name;
            }
            $result = implode($separator, $nameList);
        }
        return $result;
    }

    public function asPercentInteger($value): string
    {
        return $value . ' %';
    }

    /**
     * @param $value
     * @return string
     */
    public function asBooleanByLabel($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        if ($value) {
            return Html::tag('span', 'Yes', ['class' => 'badge badge-success']);
        }
        return Html::tag('span', 'No', ['class' => 'badge badge-danger']);
    }

    public function asBooleanBySquare($value, bool $color = true): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        if ($value) {
            $classColor = $color ? 'text-success' : '';
            return Html::tag('i', '', ['class' => 'fa fa-check-square ' . $classColor]);
        }
        $classColor = $color ? 'text-danger' : '';
        return Html::tag('i', '', ['class' => 'fa fa-minus-square ' . $classColor]);
    }

    /**
     * @param $value
     * @return string
     */
    public function asClient($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::a($value, ['/client/view', 'id' => $value], ['data-pjax' => 0, 'target' => '_blank']);
    }

    public function asCouponStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return CouponStatus::asFormat($value);
    }

    public function asCouponType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return CouponType::asFormat($value);
    }

    public function asCoupon($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-ticket']) . ' ' . Html::a($value, ['/coupon/view', 'id' => $value], ['data-pjax' => 0, 'target' => '_blank']);
    }

    public function asConferenceParticipantType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ConferenceParticipant::getTypeName($value);
    }

    /**
     * @param int|null $userId
     * @return string
     */
    public function asUserNameLinkToUpdate(?int $userId): string
    {
        if ($user = Employee::find()->select(['username'])->where(['id' => $userId])->cache(3600)->one()) {
            $result = Html::a(
                Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' .
                    Html::encode($user->username) . ' ID:' . $userId,
                ['employee/update', 'id' => $userId],
                ['target' => '_blank']
            );
        } else {
            $result = $this->nullDisplay;
        }
        return $result;
    }

    /**
     * @param $status
     * @return string
     */
    public function asEmployeeStatusLabel($status): string
    {
        if ($status === null) {
            return $this->nullDisplay;
        }

        switch ($status) {
            case Employee::STATUS_ACTIVE:
                $result = Html::tag('span', Employee::STATUS_LIST[Employee::STATUS_ACTIVE], ['class' => 'label label-success']);
                break;
            case Employee::STATUS_DELETED:
                $result = Html::tag('span', Employee::STATUS_LIST[Employee::STATUS_DELETED], ['class' => 'label label-danger']);
                break;
            case Employee::STATUS_BLOCKED:
                $result = Html::tag('span', Employee::STATUS_LIST[Employee::STATUS_BLOCKED], ['class' => 'label label-warning']);
                break;
            default:
                $result = Html::tag('span', 'not found', ['class' => 'label label-info']);
        }
        return $result;
    }

    public function asConferenceParticipantStatus($statusId): string
    {
        if ($statusId === null) {
            return $this->nullDisplay;
        }

        return ConferenceParticipant::getStatusName($statusId);
    }

    public function asClientChat(?ClientChat $chat): string
    {
        if ($chat === null) {
            return $this->nullDisplay;
        }

        return \src\model\clientChat\Formatter::asClientChat($chat);
    }

    public function asShift(?Shift $model): string
    {
        if ($model === null) {
            return $this->nullDisplay;
        }

        return \yii\helpers\Html::a(
            'shift: ' . $model->sh_name . '(' . $model->sh_id . ')',
            Url::to(['/shift-crud/view', 'id' => $model->sh_id]),
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }

    /**
     * @param Employee|int|string|null $value
     * @return string
     */
    public function asUserNickname($value): string
    {
        if (!$value) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            $name = $value;
        } elseif ($value instanceof Employee) {
            $name = $value->nickname ?: $value->username;
        } elseif (is_int($value)) {
            if ($entity = Employee::find()->select(['nickname', 'username'])->where(['id' => $value])->cache(3600)->one()) {
                $name = $entity->nickname ?: $entity->username;
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('user must be Employee|int|string|null');
        }

        return Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($name);
    }

    public function asPhoneOrNickname($value): string
    {
        if (!$value) {
            return $this->nullDisplay;
        }

        return PhoneFormatter::getPhoneOrNickname($value);
    }

    public function asDumpJson(?string $data): string
    {
        if ($data) {
            return VarDumper::dumpAsString($data);
        }
        return $this->nullDisplay;
    }

    public function asFileLogType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return FileLogType::asFormat($value);
    }

    public function asFileStorageStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return FileStorageStatus::asFormat($value);
    }

    public function asRelativeDt($value, $minHours = 3, $maxHours = 73): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $createdTS = strtotime($value);
        $diffTime = time() - $createdTS;
        $diffHours = (int) ($diffTime / (60 * 60));

        $result = ($diffHours > $minHours && $diffHours < $maxHours) ? $diffHours . ' hours' : $this->asRelativeTime($createdTS);
        return $result . '<br /> <i class="fa fa-calendar"></i> ' . $this->asDatetime(strtotime($value));
    }

    public function asExpirationDt($value, $minHours = 3, $maxHours = 73): string
    {
        $relative = $this->asRelativeDt($value, $minHours, $maxHours);
        if ($relative === $this->nullDisplay) {
            return $relative;
        }
        if ((strtotime($value) < time())) {
            return '<span class="label-warning label" title="' . strip_tags($relative) . '">expired</span>';
        }
        return $relative;
    }

    public function asProductQuoteChangeStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteChangeStatus::asFormat($value);
    }

    public function asProductQuoteChangeDecisionType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteChangeDecisionType::asFormat($value);
    }

    public function asClientNotificationType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \src\model\client\notifications\client\entity\NotificationType::asFormat($value);
    }

    public function asClientNotificationCommunicationType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \src\model\client\notifications\client\entity\CommunicationType::asFormat($value);
    }

    public function asClientNotificationPhoneListStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \src\model\client\notifications\phone\entity\Status::asFormat($value);
    }

    public function asClientNotificationSmsListStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \src\model\client\notifications\sms\entity\Status::asFormat($value);
    }

    public function asUserDataKey($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \src\model\userData\entity\UserDataKey::asFormat($value);
    }

    public function asLabel($value, string $class = 'label label-default'): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return Html::tag('span', $value, ['class' => $class]);
    }

    public function asPhoneDeviceLogLevel($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \src\model\voip\phoneDevice\log\PhoneDeviceLogLevel::asFormat($value);
    }

    public function asFormattedPhoneNumber(string $number): string
    {
        if (preg_match('/^\+?[0-9]{11}$/', $number)) {
            return '+' . substr($number, 1, 1) .
                   ' (' . substr($number, 2, 3) . ') ' .
                   '<span style="color:green">' .
                   substr($number, 5, 3) .
                   '</span>
                 -
                 <span style="color:blue">' .
                   substr($number, 8, 4) .
                   '</span>';
        }
        return $number;
    }

    /**
     * @param $value
     * @param string $code
     * @param int $precision
     * @param int $decimals
     * @param string|null $decimal_separator
     * @param string|null $thousands_separator
     * @return string
     */
    public function asNumCurrency(
        $value,
        string $code = 'USD',
        int $precision = 2,
        int $decimals = 2,
        ?string $decimal_separator = '.',
        ?string $thousands_separator = ','
    ): string {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $num = number_format(
            round((float) $value, $precision),
            $decimals,
            $decimal_separator,
            $thousands_separator
        );

        return $code . ' ' . $num;
    }

    /**
     * @param int $minutes
     * @param string $format
     * @return string
     */
    public function asHoursDuration(int $minutes, string $format = '%02dh %02dm'): string
    {
        $partOfHours = intdiv($minutes, 60);
        $partOfMinutes = $minutes % 60;
        return sprintf($format, $partOfHours, $partOfMinutes);
    }
}
