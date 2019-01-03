<?php
/**
 *  The base of all objects.
 *
 *  @author         Tycho Atsma  <tycho.atsma@copernica.com>
 *  @file           VObject.php
 *  @documentation  ignore
 */

namespace iCalendar;

/**
 *  The interface definition
 */
abstract class VObject implements VComponent
{
    /**
     *  An array of all properties that belong to this
     *  object. Which properties are supported are
     *  defined by the return value of the
     *  ::defaults method.
     *
     *  @var  array
     */
    private $properties = array();

    /**
     *  An array of all other objects that belong to this
     *  object.
     *
     *  @var  array
     */
    private $objects = array();

    /**
     *  Get the type of this object.
     *  e.g.  VCALENDAR
     *
     *  @return  string
     */
    abstract public function type();

    /**
     *  Get a list of supported default properties 
     *  of this object.
     *
     *  This needs to be implemented by all child
     *  classes as this is used to determine
     *  if a property can be added or not.
     *
     *  @return  array
     */
    abstract public function defaults();

    /**
     *  The constructor.
     *
     *  @param  string|null  a serialized string
     */
    public function __construct($serialized = null)
    {
        // assign all the default values
        $this->properties = $this->defaults();

        // unserialize if we received a serialized string
        if (!is_null($serialized)) $this->unserialize($serialized);
    }

    /**
     *  Add something new to the object. This can be
     *  a property, or another object.
     *
     *  @note   This method can throw
     *
     *  @param  VObject  the object to add
     *  or
     *  @param  string   the property to add
     *  @param  string   the value of the property
     *
     *  @return  VObject
     */
    public function add()
    {
        // get the function arguments
        $args = func_get_args();

        // is the first argument an object?
        if ($args[0] instanceof VObject)
        {
            // add the object to the list of objects
            $this->objects[] = $args[0];
        }

        // is it not an object, then we require two strings:
        // a property name and value
        else
        {
            // we need two parameters
            if (count($args) < 2)
            {
                // we throw an exception here as it is a bad method call
                // which we need to be very strict about
                throw new \Exception('Missing parameters: At least two are required.');
            }

            // we need them to be strings
            elseif (!is_string($args[0]) || !is_string($args[1]))
            {
                // we throw an exception here as it is a bad method call
                // which we need to be very strict about
                throw new \Exception('Invalid parameter: Argument has to be a string.');
            }

            // we also need to check if the property exists in the array of defaults.
            // we don't want to allow unsupported properties to be set
            elseif (!array_key_exists(strtoupper($args[0]), $this->defaults()))
            {
                // we throw an exception here as it is a bad method call
                // which we need to be very strict about
                throw new \Exception("Invalid parameter: Property '{$args[0]}' is not supported.");
            }

            // we can now safely set the parameters
            $this->properties[strtoupper($args[0])] = $args[1];
        }

        // allow chaining
        return $this;
    }

    /**
     *  Alias for adding a new property on an object.
     *
     *  @param   string  the name of the property
     *  @param   string  the value of the property
     *  @return  iCalendar\VObject
     */
    public function set($name, $value)
    {
        return $this->add($name, $value);
    }

    /**
     *  Remove something from the object. This can
     *  be a property, or another object.
     *
     *  NOTE:    Objects added through unserialization
     *           can't be removed by object reference.
     *
     *  @todo    Implement removal of objects by reference
     *           added through unserialization.
     *
     *  @param   VObject  the object to remove
     *  or
     *  @param   string   the property to remove
     *
     *  @return  VObject
     */
    public function remove()
    {
        // get the function arguments
        $args = func_get_args();

        // is the first argument an object?
        if ($args[0] instanceof VObject)
        {
            // find the object
            $index = array_search($args[0], $this->objects);

            // remove the object to the list of objects if it exists. we do
            // not throw here as this is not an invalid operation. we don't
            // want to stop the code execution
            if ($index !== false) array_splice($this->objects, $index, 1);
        }

        // is it not an object, then we require a single string
        else
        {
            // is the first argument not a string?
            if (!is_string($args[0]))
            {
                // we throw an exception here as it is a bad method call
                // which we need to be very strict about
                throw new \Exception('Invalid parameter: Argument has to be a string.');
            }

            // we also need to check if the property exists in the array of defaults.
            // otherwise there is no point in nullifying it
            elseif (!array_key_exists(strtoupper($args[0]), $this->defaults()))
            {
                // we throw an exception here as it is a bad method call
                // which we need to be very strict about
                throw new \Exception("Invalid parameter: Property '{$args[0]}' is not supported.");
            }

            // nullify the property so it is not serialized
            $this->properties[strtoupper($args[0])] = null;
        }

        // allow chaining
        return $this;
    }

    /**
     *  Get the list of all properties that are installed
     *  on this object.
     *
     *  @return  array
     */
    public function properties()
    {
        return $this->properties;
    }

    /**
     *  Get the list of all objects that are installed
     *  on this object.
     *
     *  @return  array
     */
    public function objects()
    {
        return $this->objects;
    }

    /**
     *  Serialize the calendar.
     *
     *  @return  string
     */
    public function serialize()
    {
        // construct the start 
        $serialized = "BEGIN:{$this->type()}\r\n";
        
        // add the properties of this object
        foreach ($this->properties() as $name => $value) 
        {
            // we only serialize values that are not null
            if (!is_null($value)) $serialized .= "{$name}:{$value}\r\n";
        }

        // loop over all objects and add them
        foreach ($this->objects() as $object) $serialized .= $object->serialize();

        // return the serialized calendar
        return $serialized . "END:{$this->type()}\r\n";
    }

    /**
     *  Unserialize a serialized calendar.
     *
     *  @param   string  the serialized calendar
     */
    public function unserialize($serialized)
    {
        // before starting, we want to clear all old objects as
        // we want to start fresh after unserializing
        $this->objects = array();

        // seperate the string based on the BEGIN line, as it
        // indicates a new event happening
        $objects = explode('BEGIN', $serialized);

        // we do not care about the first or the second index, as the first
        // one contains an empty string and the second one the type
        // data, which we already have
        array_splice($objects, 0, 2);

        // the last element of the string will contain the end indication
        // of the object type, which we also do not need.
        if (count($objects))
        {
            $last = count($objects) - 1;
            $objects[$last] = str_replace("END:{$this->type()}", '', $objects[$last]);
        }

        // for now, we only add objects if it's a calendar as that
        // is the only object that can contain other objects
        if ($this instanceof VCalendar)
        {
            // start adding every object
            foreach ($objects as $object)
            {
                // construct the new event object. this part needs to be extended
                // in the future when we support multiple types of objects. for
                // now, we only support VEvent objects.
                $event = new VEvent($object);

                // add the object to the calendar
                $this->add($event);
            }
        }

        // otherwise, parse the serialized string and set the properties of
        // this object
        else $this->properties = $this->parse($serialized);
    }

    /**
     *  Parse a serialized object.
     *
     *  @param   string  the serialized representation
     *  @return  array
     */
    private function parse($serialized = null)
    {
        // initiate the properties
        $properties = $this->defaults();

        // we dont have anything?
        if (is_null($serialized)) return $properties;

        // explode all parameters
        $params = explode("\n", $serialized);

        // loop over all the parameters
        foreach ($params as $parameter)
        {
            // split the string on the delimiter
            $sep = explode(':', $parameter);

            // do we have this parameter?
            if (array_key_exists($sep[0], $properties)) $properties[$sep[0]] = $sep[1];
        }

        // return the properties
        return $properties;
    }
}
