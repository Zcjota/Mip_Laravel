<?php

/**
 * Definir una clase de excepción personalizada
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class ExceptionValidate extends Exception
{
    private $validate;
    public function __construct(string $message, ValidationValidator $validator = null, Exception $previous = null, $code = 0)
    {
        // algo de código
        $this->validate = $validator;
        // asegúrese de que todo está asignado apropiadamente
        parent::__construct($message, $code, $previous);
    }

    // representación de cadena personalizada del objeto
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function error()
    {
        if ($this->validate == null) return null;
        return $this->validate->errors();
    }
}
