<?php
namespace webapi\src\services\communication;

class RequestDataDTO
{
	public string $Called;
	public string $ToState;
	public string $CallerCountry;
	public string $Direction;
	public string $CallerState;
	public string $ToZip;
	public string $CallSid;
	public string $To;
	public string $CallerZip;
	public string $ToCountry;
	public string $ApiVersion;
	public string $CalledZip;
	public string $CalledCity;
	public string $CallStatus;
	public string $From;
	public string $AccountSid;
	public string $CalledCountry;
	public string $CallerCity;
	public string $ApplicationSid;
	public string $Caller;
	public string $FromCountry;
	public string $ToCity;
	public string $FromCity;
	public string $CalledState;
	public string $FromZip;
	public string $FromState;
	public string $FromAgentPhone;
	public int $projectId;
	public string $ParentCallSid;
	public int $Digits;
	public int $c_user_id;
	public int $SequenceNumber;
	public int $CallDuration;
	public string $ForwardedFrom;
	public string $RecordingSid;
	public string $FinishedOnKey;
	public string $msg;
	public array $callData;
	public array $call;

	public function __construct(array $requestData)
	{
		$this->Called = $requestData['Called'] ?? '';
		$this->ToState = $requestData['ToState'] ?? '';
		$this->CallerCountry = $requestData['CallerCountry'] ?? '';
		$this->Direction = $requestData['Direction'] ?? '';
		$this->CallerState = $requestData['CallerState'] ?? '';
		$this->ToZip = $requestData['ToZip'] ?? '';
		$this->CallSid = $requestData['CallSid'] ?? '';
		$this->To = $requestData['To'] ?? '';
		$this->CallerZip = $requestData['CallerZip'] ?? '';
		$this->ToCountry = $requestData['ToCountry'] ?? '';
		$this->ApiVersion = $requestData['ApiVersion'] ?? '';
		$this->CalledZip = $requestData['CalledZip'] ?? '';
		$this->CalledCity = $requestData['CalledCity'] ?? '';
		$this->CallStatus = $requestData['CalledStatus'] ?? '';
		$this->From = $requestData['From'] ?? '';
		$this->AccountSid = $requestData['AccountSid'] ?? '';
		$this->CalledCountry = $requestData['CalledCountry'] ?? '';
		$this->CallerCity = $requestData['CallerCity'] ?? '';
		$this->ApplicationSid = $requestData['ApplicationSid'] ?? '';
		$this->Caller = $requestData['Caller'] ?? '';
		$this->FromCountry = $requestData['FromCountry'] ?? '';
		$this->ToCity = $requestData['ToCity'] ?? '';
		$this->FromCity = $requestData['FromCity'] ?? '';
		$this->CalledState = $requestData['CalledState'] ?? '';
		$this->FromZip = $requestData['fromZip'] ?? '';
		$this->FromState = $requestData['FromState'] ?? '';
		$this->FromAgentPhone = $requestData['FromAgentPhone'] ?? '';
		$this->projectId = (int)($requestData['project_id'] ?? 0);
		$this->ParentCallSid = $requestData['ParentCallSid'] ?? '';
		$this->Digits = (int)($requestData['Digits'] ?? 0);
		$this->c_user_id = (int)($requestData['c_user_id'] ?? 0);
		$this->SequenceNumber = (int)($requestData['SequenceNumber'] ?? 0);
		$this->CallDuration = (int)($requestData['CallDuration'] ?? 0);
		$this->ForwardedFrom = $requestData['ForwardedFrom'] ?? '';
		$this->RecordingSid = $requestData['RecordingSid'] ?? '';
		$this->FinishedOnKey = $requestData['FinishedOnKey'] ?? '';
		$this->msg = $requestData['msg'] ?? '';
		$this->callData = $requestData['callData'] ?? [];
		$this->call = $requestData['call'] ?? [];

	}
}