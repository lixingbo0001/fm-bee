<?php

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string $abstract
     * @param  array $parameters
     * @return mixed|\Core\Application
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return \Core\Application::app();
        }

        return \Core\Application::app()->make($abstract, $parameters);
    }
}

if (!function_exists('validator')) {
    /**
     * Create a new Validator instance.
     *
     * @param  array $data
     * @param  array $rules
     * @param  array $messages
     * @param  array $customAttributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $loader = new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem(), app('path.lang'));

        return new \Illuminate\Validation\Validator(new \Illuminate\Translation\Translator($loader, 'en'), $data, $rules, $messages, $customAttributes);
    }
}

function basePath($path = null)
{
    return app()->make('path.base') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
}

function configPath($path = null)
{
    return app()->make('path.config') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
}

function bootstrapPath($path = null)
{
    return app()->make('path.bootstrap') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
}

function routePath($path = null)
{
    return app()->make('path.route') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
}

function appPath($path = null)
{
    return app()->make('path.app') . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
}

function appEnv()
{
    return config('app.app_env');
}

function isDev()
{
    return appEnv() == 'dev' || !(isProd() || isTest());
}

function isProd()
{
    return appEnv() == 'prod';
}

function isTest()
{
    return appEnv() == 'test';
}

if (!function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        return \Illuminate\Support\Arr::get($array, $key, $default);
    }
}

if (!function_exists('array_forget')) {

    function array_forget(&$array, $keys)
    {
        \Illuminate\Support\Arr::forget($array, $keys);
    }
}

/**
 * @return \Core\Contracts\ResponseInterface
 */
function myResponse()
{
    return app('response');
}

/**
 * @return \Core\Request\Request
 */
function request()
{
    return app('request');
}

/**
 * @param null $key
 * @param null $default
 * @return \Core\Application|mixed|object|array
 */
function config($key = null, $default = null)
{

    if (is_null($key)) {
        return app('config');
    }

    if (is_array($key)) {
        return app('config')->set($key);
    }

    return app('config')->get($key, $default);
}