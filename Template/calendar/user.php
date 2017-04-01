<?= $this->calendar->render(
    $this->url->href('CalendarController', 'userEvents', array('user_id' => $user['id'], 'plugin' => 'Calendar')),
    $this->url->href('CalendarController', 'save', array('plugin' => 'Calendar'))
) ?>
