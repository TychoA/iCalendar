<?php
/**
 *  The base implementation of the VCalendar object.
 *
 *  @author         Tycho Atsma  <tycho.atsma@copernica.com>
 *  @documentation  ignore
 */

namespace iCalendar;

/**
 *  The class definition
 */
class VCalendar extends VObject
{
    /**
     *  Get the type of this object.
     *
     *  @return  string
     */
    public function type()
    {
        return 'VCALENDAR';
    }

    /**
     *  Get a list of supported default properties 
     *  of this object.
     *
     *  @return  array
     */
    public function defaults()
    {
        return array(
            'PRODID'   => "-//Copernica BV//Copernica Calendar //NL",
            'VERSION'  => "2.0",
            'CALSCALE' => "GREGORIAN",
            'METHOD'   => "PUBLISH",
        );
    }
}
