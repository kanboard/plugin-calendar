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
                        var origDate = new Date(val._start._d);
                        destDate = new Date(origDate.getTime() + origDate.getTimezoneOffset() * 60 * 1000);

                        //
                        // date_due building
                        //
                        var destMonth = ('0' + (destDate.getMonth() + 1)).slice(-2);
                        var destDay = ('0' + destDate.getDate()).slice(-2);
                        var destHours = ('0' + (destDate.getHours())).slice(-2);
                        var destMinutes = ('0' + destDate.getMinutes()).slice(-2);
                        var date_due = destDate.getFullYear() + '-' + destMonth + '-' + destDay + ' ' + destHours + ':' + destMinutes;
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
