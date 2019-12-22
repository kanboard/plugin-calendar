<?php

namespace Kanboard\Plugin\Calendar;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Plugin\Calendar\Formatter\ProjectApiFormatter;
use Kanboard\Plugin\Calendar\Formatter\TaskCalendarFormatter;

class Plugin extends Base
{
    public function initialize()
    {
        $this->helper->register('calendar', '\Kanboard\Plugin\Calendar\Helper\CalendarHelper');

        $this->container['taskCalendarFormatter'] = $this->container->factory(function ($c) {
            return new TaskCalendarFormatter($c);
        });

        $this->container['projectApiFormatter'] = $this->container->factory(function ($c) {
            return new ProjectApiFormatter($c);
        });

        $this->template->hook->attach('template:dashboard:page-header:menu', 'Calendar:dashboard/menu');
        $this->template->hook->attach('template:project:dropdown', 'Calendar:project/dropdown');
        $this->template->hook->attach('template:project-header:view-switcher', 'Calendar:project_header/views');
        $this->template->hook->attach('template:config:sidebar', 'Calendar:config/sidebar');

        $this->hook->on('template:layout:css', array('template' => 'plugins/Calendar/Assets/fullcalendar.min.css'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Calendar/Assets/moment.min.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Calendar/Assets/fullcalendar.min.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Calendar/Assets/locale-all.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Calendar/Assets/calendar.js'));
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        return 'Calendar';
    }

    public function getPluginDescription()
    {
        return t('Calendar view for Kanboard');
    }

    public function getPluginAuthor()
    {
        return 'Frédéric Guillot';
    }

    public function getPluginVersion()
    {
        return '1.1.1';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/plugin-calendar';
    }

    public function getCompatibleVersion()
    {
        return '>=1.2.13';
    }
}

