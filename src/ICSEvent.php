<?php

namespace Armetiz\SimpleICS;

use Assert\Assertion;

class ICSEvent
{
    /** @var string */
    private $uid;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var array */
    private $parameters;

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(array $parameters, string $tld = 'armetiz.info')
    {
        $this->assertParameters($parameters);
        $this->parameters = $parameters;

        $this->uid = uniqid(random_int(PHP_INT_MIN, PHP_INT_MAX), false) . '@' . $tld;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function assertParameters(array $parameters): void
    {
        Assertion::keyExists($parameters,'startAt');
        Assertion::keyExists($parameters,'endAt');
        Assertion::keyExists($parameters,'summary');
        Assertion::keyExists($parameters,'description');
        Assertion::keyExists($parameters,'location');
        Assertion::keyExists($parameters,'organizer');
        Assertion::keyExists($parameters,'attendees');
        Assertion::keyExists($parameters,'alarms');

        Assertion::isInstanceOf($parameters['startAt'], \DateTimeInterface::class);
        Assertion::isInstanceOf($parameters['endAt'], \DateTimeInterface::class);
        Assertion::true($parameters['startAt'] <= $parameters['endAt']);
        Assertion::string($parameters['summary']);
        Assertion::string($parameters['description']);
        Assertion::string($parameters['location']);
        Assertion::isArray($parameters['organizer']);
        Assertion::keyExists($parameters['organizer'], 'email');
        Assertion::keyExists($parameters['organizer'], 'name');
        Assertion::email($parameters['organizer']['email']);
        Assertion::string($parameters['organizer']['name']);
        Assertion::isArray($parameters['attendees']);
        Assertion::allEmail(array_keys($parameters['attendees']));
        Assertion::allString($parameters['attendees']);
    }

    final protected function formatDate(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Ymd\THis\Z');
    }

    public function __toString(): string
    {
        return $this->output();
    }

    public function output(): string
    {
        $content = "BEGIN:VCALENDAR\n";
        $content .= "PRODID:-//Armetiz // SimpleICS//EN\n";
        $content .= "VERSION:1.0\n";
        $content .= "CALSCALE:GREGORIAN\n";
        $content .= "METHOD:REQUEST\n";
        $content .= "BEGIN:VEVENT\n";
        $content .= "DTSTART:{$this->formatDate($this->parameters['startAt'])}\n";
        $content .= "DTEND:{$this->formatDate($this->parameters['endAt'])}\n";
        $content .= "DTSTAMP:{$this->formatDate($this->createdAt)}\n";
        $content .= "ORGANIZER;CN={$this->parameters['organizer']['name']}:mailto:{$this->parameters['organizer']['email']}\n";
        $content .= "UID:{$this->uid}\n";

        foreach ($this->parameters['attendees'] as $email => $name) {
            $content .= "ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={$name};X-NUM-attendees=0:mailto:{$email}\n";
        }

        $content .= "CREATED:{$this->formatDate($this->createdAt)}\n";
        $content .= "DESCRIPTION:{$this->parameters['description']}\n";
        $content .= "LAST-MODIFIED:{$this->formatDate($this->createdAt)}\n";
        $content .= "LOCATION:{$this->parameters['location']}\n";
        $content .= "SUMMARY:{$this->parameters['summary']}\n";
        $content .= "SEQUENCE:0\n";
        $content .= "STATUS:NEEDS-ACTION\n";
        $content .= "TRANSP:OPAQUE\n";


//        $content .= $this->generateAlarmSection($this->parameters['startAt']->modify('-1 day'), $this->parameters['summary']);

        $content .= "END:VEVENT\n";
        $content .= "END:VCALENDAR\n";

        return $content;
    }

//    private function generateAlarmSection(\DateTimeInterface $triggerAt, string $summary): string
//    {
//        $content = "BEGIN:VALARM\n";
//        $content .= "TRIGGER:{$this->formatDate($triggerAt)}\n";
//        $content .= "DESCRIPTION:{$summary}\n";
//        $content .= "ACTION:DISPLAY\n";
//        $content .= "END:VALARM\n";
//
//        return $content;
//    }
}