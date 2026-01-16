<?php

namespace App\Core;

class HookSystem
{
    protected static $actions = [];
    protected static $filters = [];

    /**
     * Add an action hook.
     *
     * @param string $hook
     * @param callable $callback
     * @param int $priority
     * @return void
     */
    public static function addAction($hook, $callback, $priority = 10)
    {
        if (!isset(self::$actions[$hook])) {
            self::$actions[$hook] = [];
        }

        self::$actions[$hook][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];

        // Sort by priority
        usort(self::$actions[$hook], function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }

    /**
     * Execute an action hook.
     *
     * @param string $hook
     * @param mixed ...$args
     * @return void
     */
    public static function doAction($hook, ...$args)
    {
        if (!isset(self::$actions[$hook])) {
            return;
        }

        foreach (self::$actions[$hook] as $action) {
            call_user_func_array($action['callback'], $args);
        }
    }

    /**
     * Add a filter hook.
     *
     * @param string $hook
     * @param callable $callback
     * @param int $priority
     * @return void
     */
    public static function addFilter($hook, $callback, $priority = 10)
    {
        if (!isset(self::$filters[$hook])) {
            self::$filters[$hook] = [];
        }

        self::$filters[$hook][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];

        // Sort by priority
        usort(self::$filters[$hook], function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }

    /**
     * Apply filter hooks to a value.
     *
     * @param string $hook
     * @param mixed $value
     * @param mixed ...$args
     * @return mixed
     */
    public static function applyFilters($hook, $value, ...$args)
    {
        if (!isset(self::$filters[$hook])) {
            return $value;
        }

        foreach (self::$filters[$hook] as $filter) {
            $value = call_user_func_array($filter['callback'], array_merge([$value], $args));
        }

        return $value;
    }
}
