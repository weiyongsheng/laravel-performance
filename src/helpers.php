<?php
use Wys\Laravel\Performance;

if (!function_exists('pf')) {

    /**
     * Return Performance
     *
     * @param null $name
     *
     * @return Wys\Laravel\Performance
     */
    function pf($name = null)
    {
        $performance = Performance::getInstance();
        if (isset($name)) {
            $performance->auto($name);
        }

        return $performance;
    }
}
