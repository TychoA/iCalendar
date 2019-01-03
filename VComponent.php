<?php
/**
 *  The base interface of a component that can be part of the
 *  calendar.
 *
 *  @author         Tycho Atsma  <tycho.atsma@copernica.com>
 *  @file           VComponent.php
 *  @documentation  ignore
 *  @copyright      Copernica BV  2018
 */

namespace iCalendar;

/**
 *  The interface definition
 */
interface VComponent extends \Serializable
{
    /**
     *  The type of this object.
     *
     *  e.g.  VCALENDAR
     *
     *  @return  string
     */
    public function type();

    /**
     *  Add something new to the object. This can be
     *  a property, or another object.
     *
     *  @param  VObject  the object to add
     *  or
     *  @param  string   the property to add
     *  @param  string   the value of the property
     *
     *  @return  VObject
     */
    public function add();

    /**
     *  Remove something from the object. This can
     *  be a property, or another object.
     *
     *  @param  VObject  the object to remove
     *  or
     *  @param  string   the property to remove
     *
     *  @return  VObject
     */
    public function remove();
}
