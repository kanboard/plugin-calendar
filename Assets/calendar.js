KB.component('calendar', function (containerElement, options) {
    var modeMapping = {
        month: 'month',
        week: 'agendaWeek',
        day: 'agendaDay'
    };

    this.render = function () {
        var calendar = $(containerElement);
        var mode = 'month';
        if (window.location.hash) { // Check if hash contains mode
            var hashMode = window.location.hash.substr(1);
            mode = modeMapping[hashMode] || mode;
        }

        calendar.fullCalendar({
            locale: $("html").attr('lang'),
            editable: true,
            eventLimit: true,
            defaultView: mode,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            eventDrop: function(event) {
                var droppedEvent = {
                    "task_id": event.id,
                    "date_due": event
                }

                function replacer(name, val) {
                    if ( (name.length == 0) || (name == 'task_id') ) { 
                        return val;
                    } else {
                        // Set the due date to something valid.
                        // Get the current HHmmss
                        var rightNow = new Date();
                        // Get the current HH zero-padded
                        var rightNowHours = (('0' + rightNow.getHours()).slice(-2));
                        // Get the current mm zero-padded
                        var rightNowMinutes = (('0' + rightNow.getMinutes()).slice(-2));
                        var rightNowTime = (rightNowHours + rightNowMinutes)

                        sourceDate = new Date(val._start._i);
                        var sourceHours = (('0' + sourceDate.getHours()).slice(-2));
                        var sourceMinutes = (('0' + sourceDate.getHours()).slice(-2));
                        var sourceTime = (sourceHours + sourceMinutes)

                        var destHours = (('0' + val._start._d.getHours()).slice(-2));
                        var destMinutes = (('0' + val._start._d.getMinutes()).slice(-2));
                        var destPrefix = (val._start._d.getFullYear() + '-' + ('0' + (val._start._d.getMonth() + 1)).slice(-2) + '-' + ('0' + val.start._d.getDate()).slice(-2) + ' ')
                        var destTime = (destHours + destMinutes)

                        //// An "all day" event is dragged to a new day (not a new time)
                        var dueDateAllDay = (((sourceTime === '0000') && (destTime === (rightNowHours + rightNowMinutes))) || (destTime === '0000')) ? true : false;
                        //// An event is dragged into a time that's not right now
                        var dueDateNewTime = (destTime !== (rightNowHours + rightNowMinutes)) ? true : false;
                        //// A specific time event is dragged to a new day (not a new time)
                        var dueDateOrigTime = ((destTime != (rightNowHours + rightNowMinutes)) && (destTime != '0000')) ? true : false;
                        // Set the due date
                        var date_due = destPrefix + (dueDateAllDay ? '00:00' : (dueDateNewTime ? (destHours + ':' + destMinutes) : (dueDateOrigTime ? (sourceHours + ':' + sourceMinutes) : 'epic fail')));
                                return date_due
                        }
                }
                $.ajax({
                    cache: false,
                    url: options.saveUrl,
                    contentType: "application/json",
                    type: "POST",
                    processData: false,
                    data: JSON.stringify(droppedEvent, replacer)
                });

                });
            },
            viewRender: function(view) {
                // Map view.name back and update location.hash
                for (var id in modeMapping) {
                    if (modeMapping[id] === view.name) { // Found
                        window.location.hash = id;
                        break;
                    }
                }
                var url = options.checkUrl;
                var params = {
                    "start": calendar.fullCalendar('getView').start.format(),
                    "end": calendar.fullCalendar('getView').end.format()
                };

                for (var key in params) {
                    url += "&" + key + "=" + params[key];
                }

                $.getJSON(url, function(events) {
                    calendar.fullCalendar('removeEvents');
                    calendar.fullCalendar('addEventSource', events);
                    calendar.fullCalendar('rerenderEvents');
                });
            }
        });
    };
});

KB.on('dom.ready', function () {
    function goToLink (selector) {
        if (! KB.modal.isOpen()) {
            var element = KB.find(selector);

            if (element !== null) {
                window.location = element.attr('href');
            }
        }
    }

    KB.onKey('v+c', function () {
        goToLink('a.view-calendar');
    });
});
