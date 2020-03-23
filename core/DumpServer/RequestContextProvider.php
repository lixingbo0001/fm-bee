<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/11/26
 * Time: 下午12:05
 */

namespace Core\DumpServer;

use Core\Request\Request;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;


class RequestContextProvider implements ContextProviderInterface
{

    /**
     * The current request.
     *
     * @var \Illuminate\Http\Request|null
     */
    private $currentRequest;

    /**
     * The variable cloner.
     *
     * @var \Symfony\Component\VarDumper\Cloner\VarCloner
     */
    private $cloner;


    public function __construct(Request $currentRequest)
    {
        $this->currentRequest = $currentRequest;
        $this->cloner         = new VarCloner;
        $this->cloner->setMaxItems(0);
    }

    /**
     * Get the context.
     *
     * @return array|null
     */
    public function getContext():?array
    {
        if ($this->currentRequest === null) {
            return null;
        }

        $controller = null;

        if ($route = $this->currentRequest->route()) {
            $controller = $route->controller;

            if (!$controller && !is_string($route->action['uses'])) {
                $controller = $route->action['uses'];
            }
        }

        return [
            'uri'        => $this->currentRequest->getUri(),
            'method'     => $this->currentRequest->getMethod(),
            'controller' => $controller ? $this->cloner->cloneVar(class_basename($controller)) : $this->cloner->cloneVar(null),
            'identifier' => spl_object_hash($this->currentRequest),
        ];
    }
}