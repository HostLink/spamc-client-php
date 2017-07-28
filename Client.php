<?php

namespace Winco\Antispam\Spamc;

/**
 * 
 * 
 *
 * @author   Inácio Corrêa <inacio.correa@winco.com.br>
 * @license  http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
 
class Client
{
	private $hostname = 'localhost';
    private $port     = '783';
	private $fifo;

    private $socket;
    
	private $protocolVersion = '1.5';

    /**
     * Creates a new socket connection to Spamassassin server
     */
    private function getSocket()
    {
        if (!empty($this->fifo)) {
            $socket = fsockopen('unix://' . $this->fifo, NULL, $errno, $errstr);
        } else {
            $socket = fsockopen($this->hostname, $this->port, $errno, $errstr);
        }
        if (!$socket) {
            throw new Exception(
                "Could not connect to SpamAssassin: {$errstr}", $errno
            );
        }
        return $socket;
    }
	
    /**
     * Sends a command to the server and returns an object with the result
     *
     */
    private function exec($cmd, $message, array $additionalHeaders = array())
    {
        $socket        = $this->getSocket();
        $message      .= "\r\n";
        $len = strlen($message);

        $cmd  = $cmd . " SPAMC/" . $this->protocolVersion . "\r\n";
        $cmd .= "Content-length: {$len}\r\n";

        if (!empty($this->user)) {
            $cmd .= "User: " .$this->user . "\r\n";
        }
		
        if (!empty($additionalHeaders)) {
            foreach ($additionalHeaders as $header => $val) {
                $cmd .= $header . ": " . $val . "\r\n";
            }
        }
        $cmd .= "\r\n";
        $cmd .= $message;
        $cmd .= "\r\n";
        $this->write($socket, $cmd);
        list($headers, $message) = $this->read($socket);
        return $this->parseOutput($headers, $message);
    }

    private function write($socket, $data)
    {
        fwrite($socket, $data);
    }

    private function read($socket)
    {
        $headers = '';
        $message = '';
        while (true) {
            $buffer   = fgets($socket, 128);
            $headers .= $buffer;
            if ($buffer == "\r\n" || feof($socket)) {
                break;
            }
        }
        while (!feof($socket)) {
            $message .= fgets($socket, 128);
        }
        fclose($socket);
        return array(trim($headers), trim($message));
    }
    /**
     * Parses SpamAssassin output ($header and $message)
     *
     * @param string $header  Output headers
     * @param string $message Output message
     *
     * @return Response Object that represents the response
     */
    private function parseOutput($header, $message)
    {
        $response = new Response();
        /*
         * Matches the first line in the output. Something like this:
         *
         * SPAMD/1.1 0 EX_OK
         */
        if (preg_match('/SPAMD\/(\d\.\d) (\d+) (.*)/', $header, $matches)) {
            $response->protocolVersion = $matches[1];
            $response->code    = $matches[2];
            $response->stringCode = $matches[3];
            if ($response->code != 0) {
                throw new \Exception(
                    $response->stringCode,
                    $response->code
                );
            }
        } else {
            throw new Exception('Parser ERROR');
        }
        if (preg_match('/Content-length: (\d+)/', $header, $matches)) {
            $response->contentLength = $matches[1];
        }
        if (preg_match(
            '/Spam: (True|False|Yes|No) ; (\S+) \/ (\S+)/',
            $header,
            $matches
        )) {
            ($matches[1] == 'True' || $matches[1] == 'Yes') ?
                $response->isSpam = true :
                $response->isSpam = false;
            $response->score    = (float) $matches[2];
            $response->thresold = (float) $matches[3];
        } else {
            /**
             * In PROCESS method with protocol version before 1.3, SpamAssassin
             * won't return the 'Spam:' field in the response header. In this case,
             * it is necessary to check for the X-Spam-Status: header in the
             * processed message headers.
             */
            if (preg_match(
                '/X-Spam-Status: (Yes|No)\, score=(\d+\.\d) required=(\d+\.\d)/',
                $header.$message,
                $matches)) {
                    ($matches[1] == 'Yes') ?
                        $response->isSpam = true :
                        $response->isSpam = false;
                    $response->score    = (float) $matches[2];
                    $response->thresold = (float) $matches[3];
                }
        }
        /* Used for report/revoke/learn */
        if (preg_match('/DidSet: (\S+)/', $header, $matches)) {
            $response->didSet = true;
        } else {
            $response->didSet = false;
        }
        /* Used for report/revoke/learn */
        if (preg_match('/DidRemove: (\S+)/', $header, $matches)) {
            $response->didRemove = true;
        } else {
            $response->didRemove = false;
        }
        $response->headers = $header;
        $response->message = $message;
        return $response;
    }
    /**
     * Pings the server to check the connection
     * 
     * @return bool
     */
    public function ping()
    {
		$socket = $this->getSocket();
        $this->write($socket, "PING SPAMC/{$this->protocolVersion}\r\n\r\n");
        list($headers, $message) = $this->read($socket);
        if (strpos($headers, "PONG") === false) {
            return false;
        }
        return true;
    }

    public function getSpamReport($message)
    {
        return $this->exec('REPORT', $message);
    }

    public function headers($message)
    {
        return $this->exec('HEADERS', $message)->message;
    }

    public function check($message)
    {
        return $this->exec('CHECK', $message);
    }


    public function process($message)
    {
        return $this->exec('PROCESS', $message);
    }

    public function symbols($message)
    {
        $result = $this->exec('SYMBOLS', $message);
        if (empty($result->message)) {
            return array();
        }
        $symbols = explode(",", $result->message);
        return array_map('trim', $symbols);
    }
}