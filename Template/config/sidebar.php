<li <?= $this->app->checkMenuSelection('ConfigController', 'show', 'Calendar') ?>>
    <?= $this->url->link(t('Calendar settings'), 'ConfigController', 'show', array('plugin' => 'Calendar')) ?>
</li>