<?php
/**
 * Definir una clase de excepción personalizada
 */
namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class ExceptionError extends Exception{}