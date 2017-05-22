<?php

namespace Kanboard\Plugin\Calendar\Formatter;

/**
 * Class ProjectApiFormatter
 *
 * @package Kanboard\Plugin\Calendar\Formatter
 */
class ProjectApiFormatter extends \Kanboard\Formatter\ProjectApiFormatter
{
    public function format()
    {
        $project = parent::format();

        if (! empty($project)) {
            $project['url']['calendar'] = $this->helper->url->to('CalendarController', 'project', array('project_id' => $project['id'], 'plugin' => 'Calendar'), '', true);
        }

        return $project;
    }
}
