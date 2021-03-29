<?php

namespace modules\order\src\services\confirmation;

use modules\order\src\entities\order\Order;

class EmailConfirmationData
{
    public function generate(Order $order): array
    {
        $projectData = [];
        if ($order->or_project_id) {
            $project = $order->project;
            $projectContactInfo = [];
            if ($project->contact_info) {
                $projectContactInfo = @json_decode($project->contact_info, true);
            }
            $projectData = [
                'name'      => $project->name,
                'url'       => $project->link,
                'address'   => $projectContactInfo['address'] ?? '',
                'phone'     => $projectContactInfo['phone'] ?? '',
                'email'     => $projectContactInfo['email'] ?? '',
            ];
        }

        return [
            'project' => $projectData,
            'project_key' => $project->project_key ?? '',
            'order' => $order->serialize(),
            'content' => '',
            'subject' => '',
        ];
    }
}
