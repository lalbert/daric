<?php

namespace Daric;

/**
 * @author lalbert
 */
class Document implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Add data to Document.
     *
     * @param array $data
     *
     * @return \Daric\Document
     */
    public function addData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }

        return $this;
    }

    /**
     * Set data to Document. If $key is an array it will overwrite all the data
     * in Document.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return \Daric\Document
     */
    public function setData($key, $value = null)
    {
        if (\is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Unset data from Document.
     *
     * $key can be an array of key, or a string. If $key is null, all data of
     * Document will be unseted.
     *
     * @param string|array $key
     *
     * @return \Daric\Document
     */
    public function unsetData($key = null)
    {
        if (is_null($key)) {
            $this->data = [];
        } elseif (\is_array($key)) {
            foreach ($key as $k) {
                $this->unsetData($k);
            }
        } else {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Retrieve data from Document. If $key is null, all data is returned.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getData($key = null)
    {
        if (\is_null($key)) {
            return $this->data;
        }

        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return;
    }

    /**
     * Check if $key exists in Document. If $key is null, check if Document is
     * not empty.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData($key = null)
    {
        if (is_null($key)) {
            return !empty($this->data);
        }

        return isset($this->data[$key]);
    }

    /**
     * Return all keys to Document.
     *
     * @return array
     */
    public function keys()
    {
        return \array_keys($this->data);
    }

    /**
     * Implementation of ArrayAccess::offsetSet().
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setData($offset, $value);
    }

    /**
     * Implementation of ArrayAccess::offsetExists().
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasData($offset);
    }

    /**
     * Implementation of ArrayAccess::offsetUnset().
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->unsetData($offset);
    }

    /**
     * Implementation of ArrayAccess::offsetGet().
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getData($offset);
    }
}
