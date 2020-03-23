<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/8/24
 * Time: 下午2:22
 */

namespace Core\Exceptions;


use Core\Exceptions\Contracts\ErrorCaptureInterface;
use Core\Exceptions\Contracts\ExceptionHandler;
use Ddup\Part\Exception\ExceptionCustomCodeAble;
use Exception;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;


class ExceptionHanlder implements ExceptionHandler, ErrorCaptureInterface
{

    public function captureException($e, $data = null, $logger = null, $vars)
    {
        if (app()->has('request')) {
            return $this->render(request(), $e);
        }

        return $this->renderForConsole(new ConsoleOutput(), $e);
    }

    public function render($request, Exception $e)
    {
        $code    = $e->getCode();
        $message = null;

        if ($e instanceof ExceptionCustomCodeAble) {
            $code = $e->getCustomCode();
        }

        if ($e instanceof MethodNotAllowedException) {
            $message = $request->method() . ' not in [' . join(',', $e->getAllowedMethods()) . ']';
        }

        return $this->returnException($e, $code, $message);
    }

    public function shouldReport(Exception $e)
    {
        return true;
    }

    public function renderForConsole($output, Exception $e)
    {
        $info = [
            $e->getMessage(), "Line " . $e->getLine(), $e->getFile(), "Code " . $e->getCode()
        ];

        $message = "<error>Error :</error>\n<info>" . join("\n", $info) . "</info>";

        $output->writeln($message);
    }

    public function report(Exception $e)
    {
    }

    public function returnException(Exception $exception, $code, $message = null)
    {
        $exceptionInfo = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];

        return myResponse()->code($code)->msg($message ?: $exception->getMessage())->data($exceptionInfo);
    }
}