<?php

namespace sales\model\call\useCase\reports;

use yii\helpers\FileHelper;

class CallReportSender
{
    private Credential $credential;

    public function __construct(Credential $credential)
    {
        $this->credential = $credential;
    }

    public function send(array $report, string $fileName): void
    {
        $file = $this->writeTmpFile($report);
        $this->sendWithSFTP($file, $fileName);
        FileHelper::unlink($file);
    }

    private function sendWithSFTP($file, $newFileName): void
    {
        try {
            $sftp = new SFTPConnection($this->credential->url, $this->credential->port);
            $sftp->login($this->credential->user, $this->credential->password);
            $sftp->uploadFile($file, $this->credential->path . '/' . $newFileName);
        } catch (\Throwable $e) {
            \Yii::error($e);
        }
    }

    private function sendWithCurl($file, $newFileName, $params)
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
}
