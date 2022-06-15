<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Notifications;
use common\models\ProjectEmployeeAccess;
use common\models\UserConnection;
use common\models\UserDepartment;
use frontend\widgets\notification\NotificationMessage;
use src\model\call\services\QueueLongTimeNotificationJobCreator;
use src\model\department\departmentPhoneProject\entity\params\QueueLongTimeNotificationParams;
use yii\db\Query;
use yii\queue\JobInterface;
use src\helpers\phone\MaskPhoneHelper;

/**
 * Class CallQueueLongTimeNotificationJob
 *
 * @property $callId
 * @property $departmentPhoneProjectId
 * @property $createdTime
 */
class CallQueueLongTimeNotificationJob extends BaseJob implements JobInterface
{
    public $callId;
    public $departmentPhoneProjectId;
    public $createdTime;

    public function __construct($callId, $departmentPhoneProjectId, $createdTime)
    {
        $this->callId = $callId;
        $this->departmentPhoneProjectId = $departmentPhoneProjectId;
        $this->createdTime = $createdTime;
        parent::__construct();
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $call = $this->findCall();
            if (
                !
                (
                $call->isStatusQueue()
                && $call->getData()->queueLongTime->departmentPhoneId === $this->departmentPhoneProjectId
                && $call->getData()->queueLongTime->createdJobTime === $this->createdTime
                )
            ) {
                return;
            }
            $params = $this->getParams();

            $users = $this->getUsers($params->roleKeys, $call->c_dep_id, $call->c_project_id);

            if ($users) {
                $this->sendDesktopMessage($users, $call);
            }

            if ($params->isActive()) {
                (new QueueLongTimeNotificationJobCreator())->create($call, $this->departmentPhoneProjectId, $params->getDelay());
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'callId' => $this->callId,
                'departmentPhoneProjectId' => $this->departmentPhoneProjectId,
            ], 'CallQueueLongTimeNotificationJob');
        }
    }

    private function sendNotifications(array $users, Call $call): void
    {
        $message = $this->createMessage($call);
        foreach ($users as $userId) {
            $notification = Notifications::create(
                $userId,
                'Call - Long Queue Time',
                $message,
                Notifications::getNotifyType(Notifications::TYPE_WARNING),
                true
            );
            if ($notification) {
                Notifications::publish('getNewNotification', ['user_id' => $userId], NotificationMessage::add($notification));
            }
        }
    }

    private function sendDesktopMessage(array $users, Call $call): void
    {
        $message = $this->createMessage($call);
        foreach ($users as $userId) {
            Notifications::publish(
                'showDesktopNotification',
                ['user_id' => $userId],
                NotificationMessage::desktopMessage(
                    $userId . '-desk',
                    'Call - Long Queue Time',
                    $message,
                    Notifications::getNotifyType(Notifications::TYPE_WARNING),
                    $message,
                    true
                )
            );
        }
    }

    private function createMessage(Call $call): string
    {
        $project = '';
        if ($call->c_project_id) {
            $project = ' ' . $call->cProject->name;
        }
        $department = '';
        if ($call->c_dep_id) {
            $department = ' ' . $call->cDep->dep_name;
        }

        $queueTime = time() - strtotime($call->c_queue_start_dt);

        $phoneFrom = MaskPhoneHelper::masking($call->c_from);

        return 'Call ID:' . $call->c_id . ' to' . $project . $department . ' from ' . $phoneFrom . ' is stuck in the queue for ' . $queueTime . ' sec.';
    }

    private function getUsers(array $roles, ?int $departmentId, ?int $projectId): array
    {
        $query = UserConnection::find()->select(['uc_user_id'])->groupBy(['uc_user_id']);

        $adminUsers = [];
        if (in_array(Employee::ROLE_ADMIN, $roles, true)) {
            $q = clone $query;
            foreach ($roles as $key => $role) {
                if ($role === Employee::ROLE_ADMIN) {
                    unset($roles[$key]);
                }
            }
            $q->andWhere([
                'uc_user_id' => (new Query())->select(['user_id'])->from('{{%auth_assignment}}')->andWhere(['item_name' => [Employee::ROLE_ADMIN]])
            ]);
            $adminUsers = array_keys($q->indexBy('uc_user_id')->column());
        }

        if ($departmentId) {
            $query->andWhere([
                'uc_user_id' => UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => $departmentId])
            ]);
        }

        if ($projectId) {
            $query->andWhere([
                'uc_user_id' => ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $projectId])
            ]);
        }

        $query->andWhere([
            'uc_user_id' => (new Query())->select(['user_id'])->from('{{%auth_assignment}}')->andWhere(['item_name' => $roles])
        ]);

        $otherUsers = array_keys($query->indexBy('uc_user_id')->column());

        return array_unique(array_merge($adminUsers, $otherUsers));
    }

    private function findCall(): Call
    {
        $call = Call::findOne($this->callId);
        if (!$call) {
            throw new \DomainException('Not found Call.');
        }
        return $call;
    }

    private function getParams(): QueueLongTimeNotificationParams
    {
        $phone = DepartmentPhoneProject::findOne($this->departmentPhoneProjectId);
        if (!$phone) {
            throw new \DomainException('Not found DepartmentPhoneProject.');
        }
        $params = @json_decode($phone->dpp_params, true);

        $queue_long_time_notification = $params['queue_long_time_notification'] ?? [];

        if (empty($queue_long_time_notification)) {
            throw new \DomainException('Not found queue_long_time_notification param.');
        }

        $params = new QueueLongTimeNotificationParams($queue_long_time_notification);

        if (!$params->enable) {
            throw new \DomainException('queue_long_time_notification is disabled.');
        }

        return $params;
    }
}
