<li <?= $this->app->checkMenuSelection('CalendarController') ?>>
    <?= $this->url->icon('calendar', t('Calendar'), 'CalendarController', 'project', array('project_id' => $project['id'], 'search' => $filters['search'], 'plugin' => 'Calendar'), false, 'view-calendar', t('Keyboard shortcut: "%s"', 'v c')) ?>
</li>