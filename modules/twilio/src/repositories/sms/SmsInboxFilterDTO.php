<?php
namespace modules\twilio\src\repositories\sms;

/**
 * Class SmsInboxFilterDTO
 * @package modules\twilio\src\repositories\sms
 */
class SmsInboxFilterDTO
{
	public ?int $project;
	public string $last_dt;
	public ?int $last_id;
	public ?int $last_n;
	public ?int $limit;
	public array $phone_list;
	public array $project_list;
	public int $order;
	public int $offset;
	public string $phone_to;
	public string $phone_from;

	public function __construct(array $filterData)
	{
		$this->project = $filterData['project'] ?? null;
		$this->last_dt = isset($filterData['last_dt']) ? date('Y-m-d H:i:s', strtotime($filterData['last_dt'])) : '';
		$this->last_id = $filterData['last_id'] ?? null;
		$this->last_n = $filterData['last_n'] ?? null;
		$this->limit = $filterData['limit'] ?? 100;
		$this->offset = $filterData['offset'] ?? 0;
		$this->phone_list = $filterData['phone_list'] ?? [];
		$this->project_list = $filterData['project_list'] ?? [];
		$this->order = $filterData['order'] ?? SORT_ASC;
		$this->phone_to = $filterData['phone_to'] ?? '';
		$this->phone_from = $filterData['phone_from'] ?? '';
	}
}