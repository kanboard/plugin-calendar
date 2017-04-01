<?= $this->projectHeader->render($project, 'CalendarController', 'project', false, 'Calendar') ?>

<?= $this->calendar->render(
    $this->url->href('CalendarController', 'projectEvents', array('project_id' => $project['id'], 'plugin' => 'Calendar')),
    $this->url->href('CalendarController', 'save', array('project_id' => $project['id'], 'plugin' => 'Calendar'))
) ?>
