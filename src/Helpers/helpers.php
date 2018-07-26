<?php

if (!function_exists('timezone')) {
    function timezone()
    {
        return app('timezone');
    }
}