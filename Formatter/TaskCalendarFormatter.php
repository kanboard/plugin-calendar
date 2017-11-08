<?php

namespace Kanboard\Plugin\Calendar\Formatter;

use DateTime;
use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Formatter\BaseFormatter;

/**
 * Calendar event formatter for task filter
 *
 * @package  Kanboard\Plugin\Calendar\Formatter
 * @author   Frederic Guillot
 */
class TaskCalendarFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Column used for event start date
     *
     * @access protected
     * @var string
     */
    protected $startColumn = '';

    /**
     * Column used for event end date
     *
     * @access protected
     * @var string
     */
    protected $endColumn = '';

    /**
     * Transform results to calendar events
     *
     * @access public
     * @param  string  $start_column    Column name for the start date
     * @param  string  $end_column      Column name for the end date
     * @return $this
     */
    public function setColumns($start_column, $end_column = '')
    {
        $this->startColumn = $start_column;
        $this->endColumn = $end_column ?: $start_column;
        return $this;
    }

    /**
     * Transform tasks to calendar events
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $events = array();

        foreach ($this->query->findAll() as $task) {
            $startDate = new DateTime();
            $startDate->setTimestamp($task[$this->startColumn]);

            $endDate = new DateTime();
            if (! empty($task[$this->endColumn])) {
                $endDate->setTimestamp($task[$this->endColumn]);
            }

            $allDay = $startDate == $endDate && $endDate->format('Hi') == '0000';
            $format = $allDay ? 'Y-m-d' : 'Y-m-d\TH:i:s';

            $events[] = array(
                'timezoneParam' => $this->timezoneModel->getCurrentTimezone(),
                'id' => $task['id'],
                'title' => t('#%d', $task['id']).' '.$task['title'],
                'backgroundColor' => $this->colorModel->getBackgroundColor($task['color_id']),
                'borderColor' => $this->colorModel->getBorderColor($task['color_id']),
                'textColor' => 'black',
                'url' => $this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])),
                'start' => $startDate->format($format),
                'end' => $endDate->format($format),
                'editable' => $allDay,
                'allday' => $allDay,
            );
        }

        return $events;
    }
}
