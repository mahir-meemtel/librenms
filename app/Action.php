<?php
namespace App;

class Action
{
    /**
     * Execute an action and return the results
     *
     * @param  string  $action
     * @param  mixed  ...$parameters
     * @return mixed
     */
    public static function execute(string $action, ...$parameters)
    {
        return app($action, $parameters)->execute();
    }
}
