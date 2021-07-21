<?php

namespace Barter;
use Exception;

class CustomException extends Exception
{
    private $_options;

    public function __construct($message,
                                $options = [],
                                $code = 0,
                                Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->_options = $options;
    }

    public function getOptions() { return $this->_options; }
}