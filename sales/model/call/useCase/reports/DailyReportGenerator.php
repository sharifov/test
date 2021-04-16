<?php

namespace sales\model\call\useCase\reports;

use common\models\Call;
use yii\db\Query;

class DailyReportGenerator
{
    public function generate(): array
    {
        $sql = <<<SQL
select `Time Stamp (UTC)`,`Call ID`,`Department`,`Status`,`Queue Time`,`Talk Time`,`Phone number`,if(`Client`='New','New',if( `Client`='Old' and calls_earlier_leads=0,'Old',if( `Client`='Old' and calls_earlier_leads>0,'Repeat','Other')))as `Lead`, `Client id`, `Trip id`
from(
select 
    cl_call_created_dt as `Time Stamp (UTC)`,
    cl_id as `Call ID`,
   
    cl.dep_name as `Department`,
    case cl_status_id
        when 1  then 'IVR'
        when 2  then 'Queue'
        when 3  then 'Ringing'
        when 4  then 'In Progress'
        when 5  then 'Completed'
        when 6  then 'Busy'
        when 7  then 'No answer'
        when 8  then 'Failed'
        when 9  then 'Canceled'
        when 10  then 'Delay'
        when 11 then 'Declined'
    end as `Status`
    ,cl.clq_queue_time as `Queue Time`
    ,coalesce(cl.clr_duration,0) as `Talk Time`
    ,cl.cl_client_id as  `Client ID`
    ,cl.cll_lead_id as `Trip ID`
 	,if ((select count(*)  from call_log  where cl_client_id = cl.cl_client_id and cl_group_id < cl.cl_group_id ) > 0, "Old", "New") as `Client`
 	,cl_phone_to as `Phone number`
    , (select count(cll_lead_id)  from call_log 
                        left join  call_log_lead as cll on cll_cl_id=cl_id
                           where cl_client_id = cl.cl_client_id and cl.cll_lead_id>cll.cll_lead_id 
        ) as `calls_earlier_leads`
      
from
    ( select *            
            from `call_log` 
            left join `call_log_record` on clr_cl_id = cl_id
            left join `call_log_queue` on clq_cl_id = cl_id
            left join  `call_log_lead` on cll_cl_id=cl_id
            left join `department` on dep_id = cl_department_id
            where cl_type_id=2 and cl_call_created_dt >= date(now()) - interval 1 day and cl_call_created_dt < date(now()) 
            and cl_phone_to in (select pl_phone_number
                                                    from phone_list
                                                    where pl_id in  (select dpp_phone_list_id 
                                                    					from projects
                                                    					left join department_phone_project on projects.id=dpp_project_id
                                                    					where project_key = "priceline")) 
    ) cl

order by `Time Stamp (UTC)` asc
)as dd
SQL;

        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        array_unshift($data, [
            'Time Stamp (UTC)',
            'Call ID',
            'Department',
            'Status',
            'Queue Time',
            'Talk Time',
            'Phone number',
            'Lead',
            'Client id',
            'Trip id',
        ]);
        return $data;
    }
}
