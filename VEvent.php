<?php
/**
 *  The base implementation of the VEvent object.
 *
 *  @author         Tycho Atsma  <tycho.atsma@copernica.com>
 *  @documentation  ignore
 */

namespace iCalendar;

// require dependencies
require_once './Tools.php';

/**
 *  The class definition.
 */
class VEvent extends VObject
{
    /**
     *  Get the type of this object.
     *
     *  @return  string
     */
    public function type()
    {
        return 'VEVENT';
    }

    /**
     *  Get a list of all supported default properties for the 
     *  event object.
     *
     *  @return  array
     */
    public function defaults()
    {
        return array(
            'DTSTART'     => null,
            'DURATION'    => null,
            'DTSTAMP'     => Tools::formatDate(new \PxtDateTime()),
            'UID'         => null,
            'DESCRIPTION' => null,
            'LOCATION'    => null,
            'SEQUENCE'    => 0,
            'SUMMARY'     => null,
            'TRANSP'      => 'OPAQUE'
        );
    }
}
