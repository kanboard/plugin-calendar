<?php

namespace Kanboard\Plugin\Calendar\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskDueDateRangeFilter;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Filter\TaskStatusFilter;
use Kanboard\Model\TaskModel;

/**
 * Calendar Controller
 *
 * @package  Kanboard\Plugin\Calendar\Controller
 * @author   Frederic Guillot
 * @author   Timo Litzbarski
 */
class CalendarController extends BaseController
{
    public function user()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->app('Calendar:calendar/user', array(
            'user' => $user,
        )));
    }

    public function project()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->app('Calendar:calendar/project', array(
            'project'     => $project,
            'title'       => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
        )));
    }

    public function projectEvents()
    {
        $projectId = $this->request->getIntegerParam('project_id');
        $startRange = $this->request->getStringParam('start');
        $endRange = $this->request->getStringParam('end');
        $search = $this->userSession->getFilters($projectId);
        $startColumn = $this->configModel->get('calendar_project_tasks', 'date_started');

        $dueDateOnlyEvents = $this->taskLexer->build($search)
            ->withFilter(new TaskProjectFilter($projectId))
            ->withFilter(new TaskDueDateRangeFilter(array($startRange, $endRange)))
            ->format($this->taskCalendarFormatter->setColumns('date_due'));

        $startAndDueDateQueryBuilder = $this->taskLexer->build($search)
            ->withFilter(new TaskProjectFilter($projectId));

        $startAndDueDateQueryBuilder
            ->getQuery()
            ->addCondition($this->getConditionForTasksWithStartAndDueDate($startRange, $endRange, $startColumn, 'date_due'));

        $startAndDueDateEvents = $startAndDueDateQueryBuilder
            ->format($this->taskCalendarFormatter->setColumns($startColumn, 'date_due'));

        $events = array_merge($dueDateOnlyEvents, $startAndDueDateEvents);

        $events = $this->hook->merge('controller:calendar:project:events', $events, array(
            'project_id' => $projectId,
            'start' => $startRange,
            'end' => $endRange,
        ));

        $this->response->json($events);
    }

    public function userEvents()
    {
        $user_id = $this->request->getIntegerParam('user_id');
        $startRange = $this->request->getStringParam('start');
        $endRange = $this->request->getStringParam('end');
        $startColumn = $this->configModel->get('calendar_project_tasks', 'date_started');

        $dueDateOnlyEvents = $this->taskQuery
            ->withFilter(new TaskAssigneeFilter($user_id))
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN))
            ->withFilter(new TaskDueDateRangeFilter(array($startRange, $endRange)))
            ->format($this->taskCalendarFormatter->setColumns('date_due'));

        $startAndDueDateQueryBuilder = $this->taskQuery
            ->withFilter(new TaskAssigneeFilter($user_id))
            ->withFilter(new TaskStatusFilter(TaskModel::STATUS_OPEN));

        $startAndDueDateQueryBuilder
            ->getQuery()
            ->addCondition($this->getConditionForTasksWithStartAndDueDate($startRange, $endRange, $startColumn, 'date_due'));

        $startAndDueDateEvents = $startAndDueDateQueryBuilder
            ->format($this->taskCalendarFormatter->setColumns($startColumn, 'date_due'));

        $events = array_merge($dueDateOnlyEvents, $startAndDueDateEvents);

        $events = $this->hook->merge('controller:calendar:user:events', $events, array(
            'user_id' => $user_id,
            'start' => $startRange,
            'end' => $endRange,
        ));

        $this->response->json($events);
    }

    public function save()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $values = $this->request->getJson();

            $this->taskModificationModel->update(array(
                'id' => $values['task_id'],
                'date_due' => substr($values['date_due'], 0, 10),
            ));
        }
    }

    protected function getConditionForTasksWithStartAndDueDate($startTime, $endTime, $startColumn, $endColumn)
    {
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        $startColumn = $this->db->escapeIdentifier($startColumn);
        $endColumn = $this->db->escapeIdentifier($endColumn);

        $conditions = array(
            "($startColumn >= '$startTime' AND $startColumn <= '$endTime')",
            "($startColumn <= '$startTime' AND $endColumn >= '$startTime')",
            "($startColumn <= '$startTime' AND ($endColumn = '0' OR $endColumn IS NULL))",
        );

        return $startColumn.' IS NOT NULL AND '.$startColumn.' > 0 AND ('.implode(' OR ', $conditions).')';
    }
}
