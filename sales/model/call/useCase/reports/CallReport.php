<?php

namespace sales\model\call\useCase\reports;

use common\models\Call;
use yii\db\Query;
use yii\helpers\FileHelper;

class CallReport
{
    private Credential $credential;

    public function __construct(Credential $credential)
    {
        $this->credential = $credential;
    }

    public function generate(array $phones, string $fileName, string $date): array
    {
        $result = $this->getResult($phones, $date);
        $file = $this->writeTmpFile($result);
        $this->send($file, $fileName);
        FileHelper::unlink($file);
        return $result;
    }

    private function send(string $file, string $newFileName): void
    {
        $this->withSFTP($file, $newFileName);
    }

    private function withSFTP($file, $newFileName)
    {
        try {
            $sftp = new SFTPConnection($this->credential->url, $this->credential->port);
            $sftp->login($this->credential->user, $this->credential->password);
            $sftp->uploadFile($file, $this->credential->path . '/' . $newFileName);
        } catch (\Throwable $e) {
            \Yii::error($e);
        }
    }

    private function withCurl($file, $newFileName, $params)
    {
        $url = $params['protocol'] . '://' . $params['url'] . ':' . $params['port'] . '/' . $params['path'] . '/' . $newFileName;

        $ch = curl_init($url);

        $fh = fopen($file, 'r');

        if ($fh) {
            curl_setopt($ch, CURLOPT_USERPWD, $params['user'] . ':' . $params['pass']);
            curl_setopt($ch, CURLOPT_UPLOAD, true);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
            curl_setopt($ch, CURLOPT_INFILE, $fh);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response) {
                return true;
            }
            return false;
        }
    }

    private function writeTmpFile($data): string
    {
        $file = \Yii::getAlias('@runtime/call_report_tmp.csv');

        $fp = fopen($file, 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        return $file;
    }

    private function getResult(array $phones, string $date): array
    {
        $fromDate =  date('Y-m-d H:i:s', strtotime($date));
        $toDate =  date('Y-m-d H:i:s', strtotime('+1 day', strtotime($date)));
        $query = (new Query())
            ->select([
                '`Time Stamp (UTC)`' => 'c_created_dt',
                '`Call ID`' => 'c_id',
                '`Call Length`',
                '`Phone number`' => 'c_to',
            ])
            ->from(Call::tableName())
            ->leftJoin(
                [
                    'ss' => (new Query())
                        ->select([
                            'c_parent_id',
                            '`Call Length`' => 'sum(c_recording_duration)'
                        ])
                        ->from(Call::tableName())
                        ->groupBy('c_parent_id')
                ],
                Call::tableName() . '.c_id = ss.c_parent_id'
            )
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(['c_to' => $phones])
            ->andWhere(['>=', 'c_created_dt', $fromDate])
            ->andWhere(['<', 'c_created_dt', $toDate])
            ->orderBy(['`Time Stamp (UTC)`' => SORT_ASC]);
        $data = $query->all();
        array_unshift($data, ['Time Stamp (UTC)', 'Call ID', 'Call Length', 'Phone number']);
        return $data;
    }
}
