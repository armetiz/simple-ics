# Simple ICS

Create a very simple ICS Event.  

Allowed parameters:
* Summary
* Description
* Location
* Start date
* End date
* Organizer name & email
* Attendees name & email
* Alarms

## Usage

```php
<?php

use Armetiz\SimpleICS\ICSEvent;

$icsEvent = new ICSEvent([
    'startAt' => new DateTimeImmutable('+3 days'),
    'endAt' => new DateTimeImmutable('+5 days'),
    'summary' => 'Work session - Thomas Tourlourat',
    'description' => 'First time work session; will be awesome!',
    'location' => 'Lyon, France',
    'organizer' => [
        'email' => 'thomas@tourlourat.com',
        'name' => 'Thomas Tourlourat',
    ],
    'attendees' => [
        'thomastourlourat@gmail.com' => 'Thomas Tourlourat',
    ],
    'alarms' => [],
], 'wozbe.com');

file_put_contents('/tmp/work-session.ics', $icsEvent->output());
```