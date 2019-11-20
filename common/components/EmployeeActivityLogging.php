<?php
namespace common\components;


use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeActivity;
use common\models\GlobalAcl;
use \Yii;
use yii\base\Behavior;
use yii\web\Application;

class EmployeeActivityLogging extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'activityLogging',
        ];
    }

    public function activityLogging()
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
            if (strpos(Yii::$app->request->getAbsoluteUrl(), 'lead/check-updates') !== false) {
                return true;
            }
            try {
                $activity = new EmployeeActivity();
                $activity->employee_id = Yii::$app->user->identity->getId();
                $activity->user_ip = Yii::$app->request->getUserIP();
                $activity->request = Yii::$app->request->getAbsoluteUrl();
                $activity->request_type = (Yii::$app->request->isGet)
                    ? 'GET' : 'POST';
                if (Yii::$app->request->isGet) {
                    $params = Yii::$app->request->get();
                } else {
                    $params = empty(Yii::$app->request->post())
                        ? Yii::$app->request->getBodyParams()
                        : Yii::$app->request->post();
                }
                if (!empty($params)) {
                    $activity->request_params = $params;
                }
                $activity->request_header = Yii::$app->request->getHeaders()->toArray();

                $activity->save();
            } catch (\Exception $ex) {
                \Yii::error(sprintf('Employee ID: %s\\n\\n%s', Yii::$app->user->identity->getId(), print_r($ex->getTraceAsString(), true)), 'EmployeeActivityLogging->activityLogging()');
            }

            $employee = Employee::findIdentity(Yii::$app->user->id);
            if ($employee->acl_rules_activated) {
                $clientIP = $this->getClientIPAddress();
                if ($clientIP === 'UNKNOWN' ||
                    (!GlobalAcl::isActiveIPRule($clientIP) &&
                        !EmployeeAcl::isActiveIPRule($clientIP, Yii::$app->user->id))
                ) {
                    Yii::$app->user->logout();
                    Yii::$app->getSession()->setFlash('danger', sprintf('Remote Address %s Denied! Please, contact your Supervision or Administrator.', $clientIP));
                }
            }
        }
    }

    private function getClientIPAddress()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipAddress = 'UNKNOWN';

        return $ipAddress;
    }
}