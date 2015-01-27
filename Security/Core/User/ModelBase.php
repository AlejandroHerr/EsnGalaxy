<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\User;

abstract class ModelBase implements \ArrayAccess
{
    public function __construct($attributes=array())
    {
        foreach ($attributes as $key => $value) {
            $func = 'set' . ucfirst($key);
            if (method_exists($this, $func)) {
                call_user_func_array(array(
                    $this,
                    $func
                ), array(
                    $value
                ));
            }
        }
    }
    public function __get($name)
    {
        $method = "get".ucwords($name);
        if(method_exists($this,$method)):
            return $this->$method();
        elseif (property_exists($this, $name)):
            return $this->$name;
        endif;
    }

    public function __set($name, $value)
    {
        $method = "set".ucwords($name);
        if(method_exists($this, $method)):
            return $this->$method($value);
        elseif (property_exists($this, $name)):
            $this->$name = $value;
        endif;
    }

    public function offsetExists($offset)
    {
        if(property_exists($this, $offset)):
            return true;
        else:
            return false;
        endif;
    }
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
    public function offsetSet($offset,$value)
    {
        return $this->__set($offset,$value);
    }
    public function offsetUnset($offset)
    {
        if(property_exists($this, $offset)):
            unset($this->$offset);
        endif;
    }
    public function __toString()
    {
        ob_start();
        var_dump($this);

        return ob_get_clean();
    }
    /**
     * Get properties as an associative array and trim null value
     *
     * @return array
     */
    public function toArray()
    {
        $array = @array_filter( get_object_vars($this) ,function ($value) {
            return $value!=null;
        });

        return $array;
    }
    /**
     * As toArray. Is any value is an array, converts it to string
     *
     * @return array
     */
    public function toArrayDb()
    {
        $array = $this->toArray();

        foreach ($array as &$value) {
            if(is_array($value)) $value = implode(',', $value);
        }

        return $array;
    }
    /**
     * Get properties as a JSON
     *
     * @return string JSON
     */
    public function serialize()
    {
        return json_encode($this->toArray());
    }
    /**
     * Set object's properties with the values of the given JSON
     *
     * @param string $json JSON with the properties
     */
    public function deszerialize($json)
    {
        $datas = json_decode($json);
        $this->__construct($json);
    }
}
