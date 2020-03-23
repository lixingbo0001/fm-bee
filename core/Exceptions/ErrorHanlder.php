<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/25
 * Time: 下午5:11
 */

namespace Core\Exceptions;


use Core\Exceptions\Contracts\ErrorCaptureInterface;

class ErrorHanlder
{
    protected $old_exception_handler;
    protected $call_existing_exception_handler = false;
    protected $old_error_handler;
    protected $call_existing_error_handler = false;
    protected $reservedMemory;
    /**
     * @var ErrorCaptureInterface
     */
    protected $client;
    protected $callback;
    protected $fatal_error_types = array(
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_CORE_WARNING,
        E_COMPILE_ERROR,
        E_COMPILE_WARNING,
        E_STRICT,
    );

    /**
     * @var array
     * Error types which should be processed by the handler.
     * A 'null' value implies "whatever error_reporting is at time of error".
     */
    protected $error_types = null;

    public function __construct($client, $callback = null, $error_types = null,
                                $__error_types = null)
    {

        error_reporting(0);

        // support legacy fourth argument for error types
        if ($error_types === null) {
            $error_types = $__error_types;
        }

        $this->client   = $client;
        $this->callback = $callback;
        $this->error_types = $error_types;
        $this->fatal_error_types = array_reduce($this->fatal_error_types, array($this, 'bitwiseOr'));
    }

    public function bitwiseOr($a, $b)
    {
        return $a | $b;
    }

    public function handleException($e, $isError = false, $vars = null)
    {
        if($e instanceof \Error){
            $this->errorToException($e);
            return ;
        }

        try{
            $response = $this->client->captureException($e, null, null, $vars);

        }catch (\Error $exception){
            echo $exception->getMessage();

            return ;
        }

        $callback = $this->callback;

        if($callback instanceof \Closure){
            $callback($response);
        }

    }

    public function handleError($type, $message, $file = '', $line = 0, $context = array())
    {
        if (error_reporting() !== 0) {
            $error_types = $this->error_types;
            if ($error_types === null) {
                $error_types = error_reporting();
            }

            if ($error_types & $type) {
                $e = new \ErrorException($message, 0, $type, $file, $line);
                $this->handleException($e, true, $context);

                return true;
            }
        }

        if ($this->call_existing_error_handler) {
            if ($this->old_error_handler !== null) {
                return call_user_func(
                    $this->old_error_handler,
                    $type,
                    $message,
                    $file,
                    $line,
                    $context
                );
            } else {
                return false;
            }
        }
        return true;
    }

    private function errorToException($e)
    {
        if($e instanceof \Error){
            $e = new \ErrorException(
                $e->getMessage(), $e->getCode(), 1,
                $e->getFile(), $e->getLine()
            );

            $this->handleException($e, false);
        }

        if(is_array($e)){
            $exception = new \ErrorException(
                @$e['message'], 0, @$e['type'],
                @$e['file'], @$e['line']
            );

            $this->handleException($exception, true);
        }
    }

    public function handleFatalError()
    {
        unset($this->reservedMemory);

        if (null === $error = error_get_last()) {
            return;
        }

        $this->errorToException($error);
    }

    /**
     * Register a handler which will intercept unhandled exceptions and report them to the
     * associated Sentry client.
     *
     * @param bool $call_existing Call any existing exception handlers after processing
     *                            this instance.
     * @return self
     */
    public function registerExceptionHandler($call_existing = true)
    {
        $this->old_exception_handler = set_exception_handler(array($this, 'handleException'));
        $this->call_existing_exception_handler = $call_existing;
        return $this;
    }

    /**
     * Register a handler which will intercept standard PHP errors and report them to the
     * associated Sentry client.
     *
     * @param bool  $call_existing Call any existing errors handlers after processing
     *                             this instance.
     * @param array $error_types   All error types that should be sent.
     * @return self
     */
    public function registerErrorHandler($call_existing = true, $error_types = null)
    {
        if ($error_types !== null) {
            $this->error_types = $error_types;
        }
        $this->old_error_handler = set_error_handler(array($this, 'handleError'), E_ALL);
        $this->call_existing_error_handler = $call_existing;
        return $this;
    }

    /**
     * Register a fatal error handler, which will attempt to capture errors which
     * shutdown the PHP process. These are commonly things like OOM or timeouts.
     *
     * @param int $reservedMemorySize Number of kilobytes memory space to reserve,
     *                                which is utilized when handling fatal errors.
     * @return self
     */
    public function registerShutdownFunction($reservedMemorySize = 10)
    {
        register_shutdown_function(array($this, 'handleFatalError'));

        $this->reservedMemory = str_repeat('x', 1024 * $reservedMemorySize);
        return $this;
    }
}
