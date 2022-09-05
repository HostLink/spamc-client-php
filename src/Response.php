<?php

namespace Spamc;

class Response
{
    public $protocolVersion;

    public $code;

    public $stringCode;

    public $length;

    public $score;

    public $thresold;

    public $isSpam;

    public $message;

    public $headers;

    public $didSet;

    public $didRemove;
}
