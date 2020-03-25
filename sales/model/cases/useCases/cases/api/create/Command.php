<?php

namespace sales\model\cases\useCases\cases\api\create;

/**
 * Class Command
 *
 * @property string $contact_email
 * @property string $contact_phone
 * @property int $category_id
 * @property string $order_uid
 * @property array $order_info
 * @property int $project_id
 * @property string|null $subject
 * @property string|null $description
 */
class Command
{
    public $contact_email;
    public $contact_phone;
    public $category_id;
    public $order_uid;
    public $order_info;
    public $project_id;
    public $subject;
    public $description;

    public function __construct(
        string $contact_email,
        string $contact_phone,
        int $category_id,
        string $order_uid,
        array $order_info,
        int $project_id,
        ?string $subject,
        ?string $description
    )
    {
        $this->contact_email = $contact_email;
        $this->contact_phone = $contact_phone;
        $this->category_id = $category_id;
        $this->order_uid = $order_uid;
        $this->order_info = $order_info;
        $this->project_id = $project_id;
        $this->subject = $subject;
        $this->description = $description;
    }
}
