<?php

namespace sales\model\cases\useCases\cases\api\create;

/**
 * Class Command
 *
 * @property string $email
 * @property string $phone
 * @property string $category
 * @property string $order_uid
 * @property string $subject
 * @property string $description
 * @property string $order_info
 * @property int $project_id
 */
class Command
{
    public $email;
    public $phone;
    public $category;
    public $order_uid;
    public $subject;
    public $description;
    public $order_info;
    public $project_id;

    public function __construct(
        $email,
        $phone,
        $category,
        $order_uid,
        $subject,
        $description,
        $order_info,
        $project_id
    )
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->category = $category;
        $this->order_uid = $order_uid;
        $this->subject = $subject;
        $this->description = $description;
        $this->order_info = $order_info;
        $this->project_id = $project_id;
    }
}
