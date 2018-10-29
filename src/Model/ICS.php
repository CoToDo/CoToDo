<?php

namespace App\Model;

class ICS
{

    /**
     * @var \DateTimeInterface
     */
    private $dueDate;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $priority;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $startIcs = array(
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//CoToDo',
        'CALSCALE:GREGORIAN',
        'BEGIN:VTODO'
    );


    /**
     * @var array
     */
    private $endIcs = array(
        'BEGIN:VALARM',
        'TRIGGER:-PT30M',
        'REPEAT:2',
        'DURATION:PT15M',
        'ACTION:DISPLAY',
        'END:VALARM',
        'END:VTODO',
        'END:VCALENDAR'
    );

    /**
     * ICS constructor.
     * @param \DateTimeInterface $dueDate
     * @param string $summary
     * @param string $priority
     * @param string $description
     */
    public function __construct(\DateTimeInterface $dueDate,string $summary,string $priority,string $description)
    {
        $this->dueDate=$dueDate;
        $this->summary=$summary;
        $this->priority=$priority;
        $this->description=$description;
    }

    /**
     * @return string
     */
    public function getFile() {
        $rows = $this->buildIcs();
        return implode("\r\n", $rows);
    }

    /**
     * Make ics file
     *
     * @return array
     */
    private function buildIcs() {

        $file=$this->startIcs;

        $file[] = "DUE:" . $this->dueDate->format(('Ymd')) . "T" . $this->dueDate->format(('His'));
        $file[] = "SUMMARY:" . $this->summary;
        $file[] = "PRIORITY:" . $this->getIcsPriority($this->priority);
        $file[] = "DESCRIPTION:" . $this->description;

        foreach ($this->endIcs as $e) {
            $file[] = $e;
        }

        return $file;
    }

    /**
     * Make ics priority from A-Z to 1-9
     *
     * @param string $priority
     * @return int
     */
    private function getIcsPriority(string $priority){

        $number=1;
        $mod=0;
        for($x=65; $x<=90; $x++){
            if($mod != 0 && $mod % 3 == 0) $number ++;
            $mod++;
            if(chr($x) == $priority) return $number;
        }

    }

}