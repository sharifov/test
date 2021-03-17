<?php

namespace sales\model\call\useCase\reports;

class WeeklyReportGenerator
{
    public function generate(): array
    {
        $sql = <<<SQL
select    
    concat(min(date(`Time Stamp (UTC)`)),'->',max(date(`Time Stamp (UTC)`)))as `Date`,
    count(*) as `Queued`,
    count(if(Status='Completed',1,null))as`Handled`, 
    count(if(Status='No answer',1,null))as`Abandoned`,
    (count(if(`Queue Time` between 0 and 30,1,null))/count(*)) *100 as `Service Level 30 Sec`,
    avg(`Talk Time`)as `Avg Handle Time`,
    avg(if(Status='Completed',`Queue Time`,null)) as `Avg Queue Answer Time`,
	avg(if(Status='No answer',`Queue Time`,null)) as `Avg Abandon Time`
from(
   select 
        `Time Stamp (UTC)`,
        `Call ID`,
        `Status`,
        `Queue Time`,
        `Talk Time`
    from(
      select 
            cl_call_created_dt as `Time Stamp (UTC)`,
            cl_id as `Call ID`,
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
            end as `Status`,
            cl.clq_queue_time as `Queue Time`,
            cl.clr_duration as `Talk Time`,
            cl.cl_phone_to as `Phone number`
    from
				( select *            
					from `call_log` 
					left join `call_log_record` on clr_cl_id = cl_id
					left join `call_log_queue` on clq_cl_id = cl_id
					left join  `call_log_lead` on cll_cl_id=cl_id
					where cl_type_id=2 and cl_call_created_dt >= date(now()) - interval 7 day and cl_call_created_dt < date(now()) 
					and cl_phone_to in (select pl_phone_number
															from phone_list
															where pl_id in  (select dpp_phone_list_id 
																				from projects
																				left join department_phone_project on projects.id=dpp_project_id
																				where project_key = "priceline")) 					
				) cl
    )as dd
 )as ff
SQL;

        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        array_unshift($data, [
            'Date',
            'Queued',
            'Handled',
            'Abandoned',
            'Service Level 30 Sec',
            'Avg Handle Time',
            'Avg Queue Answer Time',
            'Avg Abandon Time'
        ]);
        return $data;
    }
}
