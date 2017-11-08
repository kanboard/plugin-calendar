<?php

namespace Kanboard\Plugin\Calendar\Helper;

use Kanboard\Core\Base;

/**
 * Calendar Helper
 *
 * @package Kanboard\Plugin\Calendar\Helper
 * @author  Frederic Guillot
 */
class CalendarHelper extends Base
{
    /**
     * Render calendar component
     *
     * @param  string $checkUrl
     * @param  string $saveUrl
     * @return string
     */
    public function render($checkUrl, $saveUrl)
    {
        $params = array(
            'checkUrl' => $checkUrl,
            'saveUrl' => $saveUrl,
        );

        return '<div class="js-calendar" data-params=\''.json_encode($params, JSON_HEX_APOS).'\'></div>';
    }
}
