<?php

/**
 * Created
 * User: alexandr
 * Date: 10/09/21
 * Time: 9:05 AM
 */

namespace common\components\antispam;

use common\models\Call;

/**
 * Class CallAntiSpamDto
 * @package common\components\antispam
 *
 * @property int $categoryId
 * @property int $departmentId
 * @property int $projectId
 * @property string $operatorName
 * @property string $callType
 * @property string $errorCode1
 * @property string $errorCode2
 * @property string $mobileCountryCode
 * @property string $mobileNetworkCode
 * @property string $callerName
 * @property string $callerType
 * @property string $countryCode
 * @property string $message
 */

class CallAntiSpamDto
{
    public int $categoryId              = 1;
    public int $departmentId            = 1;
    public int $projectId               = 1;
    public string $operatorName         = "T-Mobile USA, Inc.";
    public string $callType             = "mobile";
    public string $errorCode1           = "nan";
    public string $mobileCountryCode    = "310";
    public string $mobileNetworkCode    = "800";
    public string $errorCode2           = "nan";
    public string $callerName           = "WIRELESS CALLER";
    public string $callerType           = "nan";
    public string $countryCode          = "US";
    public string $message              = "success. data from response";

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            "cl_category_id"        => $this->categoryId,
            "cl_department_id"      => $this->departmentId,
            "cl_project_id"         => $this->projectId,
            "operator_name"         => $this->operatorName,
            "call_type"             => $this->callType,
            "error_code1"           => $this->errorCode1,
            "mobile_country_code"   => $this->mobileCountryCode,
            "mobile_network_code"   => $this->mobileNetworkCode,
            "error_code2"           => $this->errorCode2,
            "caller_name"           => $this->callerName,
            "caller_type"           => $this->callerType,
            "country_code"          => $this->countryCode,
            "message"               => $this->message,
        ];
        return $data;
    }

    public static function fillFromCallTwilioResponse(array $data, Call $call): CallAntiSpamDto
    {
        $model = new self();
        $model->operatorName = $data['result']['result']['carrier']['name'] ?? 'nan';
        $model->callType = $data['result']['result']['carrier']['type'] ?? 'nan';
        $model->errorCode1 = $data['result']['result']['carrier']['error_code'] ?? 'nan';
        $model->mobileCountryCode = $data['result']['result']['carrier']['mobile_country_code'] ?? 'nan';
        $model->mobileNetworkCode = $data['result']['result']['carrier']['mobile_network_code'] ?? 'nan';
        $model->errorCode2 = $data['result']['result']['callerName']['error_code'] ?? 'nan';
        $model->callerName = $data['result']['result']['callerName']['caller_name'] ?? 'nan';
        $model->callerType = $data['result']['result']['callerName']['caller_type'] ?? 'nan';
        $model->countryCode = $data['result']['result']['countryCode'] ?? 'nan';
        $model->message = $data['result']['message'] ?? 'nan';

        $model->categoryId = $call->c_source_type_id ?? Call::SOURCE_GENERAL_LINE;
        $model->departmentId = $call->c_dep_id;
        $model->projectId = $call->c_project_id;
        return $model;
    }
}
