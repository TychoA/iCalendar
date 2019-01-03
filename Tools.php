<?php
/**
 *  A collection of tools for the icalendar.
 *
 *  @author         Tycho Atsma  <tycho.atsma@copernica.com>
 *  @documentation  ignore
 */

namespace iCalendar;

/**
 *  The class definition.
 */
abstract class Tools
{
    /**
     *  Format a datetime object to an date format that icalendar
     *  understands. These formats are used by properties as:
     *  "DTSTART".
     *
     *  @param   PxtDateTime  the date
     *  @return  string
     */
    static public function formatDate(\PxtDateTime $date)
    {
        return sprintf('%sT%s', $date->format('Ymd'), $date->format('His'));
    }
}
