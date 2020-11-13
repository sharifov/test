<?php

namespace sales\model\call\useCase\reports;

class SFTPConnection
{
    private $connection;
    private $sftp;

    public function __construct($host, $port = 22)
    {
        $this->connection = @ssh2_connect($host, $port);
        if (!$this->connection) {
            throw new \Exception("Could not connect to $host on port $port.");
        }
    }

    public function login($username, $password): void
    {
        if (!@ssh2_auth_password($this->connection, $username, $password)) {
            throw new \Exception("Could not authenticate with username $username " .
                "and password $password.");
        }

        $this->sftp = @ssh2_sftp($this->connection);
        if (!$this->sftp) {
            throw new \Exception("Could not initialize SFTP subsystem.");
        }
    }

    public function uploadFile($local_file, $remote_file): void
    {
        $sftp = $this->sftp;
        $remoteStream = @fopen("ssh2.sftp://{$sftp}/" . $remote_file, 'w');

        if (!$remoteStream) {
            throw new \Exception("Could not open file: $remote_file");
        }

        $localStream = @fopen($local_file, 'r');
        if ($localStream === false) {
            @fclose($remoteStream);
            throw new \Exception("Could not open local file: $local_file.");
        }

        if (@stream_copy_to_stream($localStream, $remoteStream) === false) {
            @fclose($remoteStream);
            @fclose($localStream);
            throw new \Exception("Could not send data from file: $local_file.");
        }

        @fclose($remoteStream);
        @fclose($localStream);
    }
}
