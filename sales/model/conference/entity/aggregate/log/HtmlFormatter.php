<?php

namespace sales\model\conference\entity\aggregate\log;

class HtmlFormatter
{
    private Logs $logs;

    public function __construct(Logs $logs)
    {
        $this->logs = $logs;
    }

    public function format(): string
    {
        $out = '<table cellspacing="5" width="1600" cellpadding="5" border="1" align="center" style="border: 1px solid #000">';
        foreach ($this->logs->logs as $log) {
            if ($log->isEvent()) {
                if ($log->participantId) {
                    $out .= '<tr bgcolor="goldenrod"><th colspan="2">Event</th><th colspan="2">ParticipantId</th><th colspan="2">Raw</t></tr>';
                    $out .= '<tr>';
                    $out .= '<td colspan="2">' . $log->type . '</td>';
                    $out .= '<td colspan="2">' . $log->participantId . '</td>';
                    $out .= '<td colspan="2"><pre>' . print_r($log->raw, true) . '</pre></td>';
                    $out .= '</tr>';
                } else {
                    $out .= '<tr bgcolor="goldenrod"><th colspan="3">Event</th><th colspan="3">Raw</th></tr>';
                    $out .= '<tr>';
                    $out .= '<td colspan="3">' . $log->type . '</td>';
                    $out .= '<td colspan="3"><pre>' . print_r($log->raw, true) . '</pre></td>';
                    $out .= '</tr>';
                }
            } elseif ($log->isParticipants()) {
                $out .= '<tr bgcolor="lightsteelblue"><th colspan="6">Participants</th></trbg>';
                $out .= '<tr><th>Id</th><th>Type</th><th>Status</th><th>Duration</th><th>TalkDuration</th><th>HoldDuration</th></tr>';
                foreach ($log->participants as $participant) {
                    $out .= '<tr>';
                    $out .= '<td>' . $participant['id'] . '</td>';
                    $out .= '<td>' . $participant['type'] . '</td>';
                    $out .= '<td>' . $this->formatStateStatuses($participant['status']) . '</td>';
                    $out .= '<td>' . $this->formatStateDuration($participant['duration']) . '</td>';
                    $out .= '<td>' . $this->formatStateDuration($participant['talkDuration']) . '</td>';
                    $out .= '<td>' . $this->formatStateDuration($participant['holdDuration']) . '</td>';
                    $out .= '</tr>';
                }
            } elseif ($log->isResult()) {
                $out .= '<tr bgcolor="indianred"><th colspan="6" style="color:#fff;">Result</th></trbg>';
                $out .= '<tr bgcolor="lightsteelblue"><th colspan="6">Participants</th></trbg>';
                $out .= '<tr><th>Id</th><th>Type</th><th>Status</th><th>Duration</th><th>TalkDuration</th><th>HoldDuration</th></tr>';
                foreach ($log->participants as $participant) {
                    $out .= '<tr>';
                    $out .= '<td>' . $participant['id'] . '</td>';
                    $out .= '<td>' . $participant['type'] . '</td>';
                    $out .= '<td>' . $this->formatStateStatuses($participant['status']) . '</td>';
                    $out .= '<td>' . $this->formatResultDuration($participant['duration']) . '</td>';
                    $out .= '<td>' . $this->formatResultDuration($participant['talkDuration']) . '</td>';
                    $out .= '<td>' . $this->formatResultDuration($participant['holdDuration']) . '</td>';
                    $out .= '</tr>';
                }
            } else {
                $out .= '<tr>';
                $out .= '<td colspan="6">Undefined log type</td>';
                $out .= '</tr>';
            }
        }
        $out .= '</table>';
        return $out;
    }

    private function formatStateStatuses(array $statuses): string
    {
        $out = [];
        foreach ($statuses as $status) {
            $out[] = '<b>' . $status['value'] . '</b><br>Start: ' . $status['start']->format('Y-m-d H:i:s') . '<br>Finish: ' . ($status['finish'] ? $status['finish']->format('Y-m-d H:i:s') : '');
        }
        return implode('<br><br>', $out);
    }

    private function formatStateDuration(array $durations): string
    {
        $out = [];
        foreach ($durations as $duration) {
            $str = 'Start: ' . $duration['start'] . '<br>' . 'Finish: ' . $duration['finish'];
            if ($duration['start'] && $duration['finish']) {
                $str .= '<br>Value: ' . (strtotime($duration['finish']) - strtotime($duration['start']));
            } elseif (!$duration['start'] && $duration['finish']) {
                $str .= '<br>Value: 0';
            }
            $out[] = $str;
        }
        return implode('<br><br>', $out);
    }

    private function formatResultDuration(array $durations): string
    {
        $out = ['<b>Value: ' . $durations['value'] . '</b>'];
        if (!empty($durations['details'])) {
            foreach ($durations['details'] as $duration) {
                $str = 'Start: ' . $duration['start'] . '<br>' . 'Finish: ' . $duration['finish'] . '<br>' . ' Value: ' . $duration['value'];
                $out[] = $str;
            }
        }
        return implode('<br><br>', $out);
    }
}
