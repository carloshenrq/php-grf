<?php
/**
 * MIT License
 * 
 * Copyright (c) 2019 Carlos Henrique
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

class GrfEntryCollection implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * All grf entries.
     * 
     * @return array
     */
    private $entries;

    /**
     * Starts the constructor for grf entries
     * 
     * @param array $entries All start entries
     * 
     * @return void
     */
    public function __construct($entries = [])
    {
        $this->entries = $entries;
    }

    /**
     * Removes the offset inside collection
     * 
     * @param mixed $offset Index inside collection
     * 
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset) === false)
            return false;

        unset($this->entries[$offset]);
    }

    /**
     * Sets the object inside collections by offset
     * 
     * @param mixed $offset Index inside collection
     * @param mixed $value  Value to make key-value collection
     * 
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (empty($offset))
            $offset = $value->getFilename();

        $this->entries[$offset] = $value;
    }

    /**
     * Gets the object inside collections by offset
     * 
     * @param mixed $offset Index inside collection
     * 
     * @return GrfEntry
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset) === false)
            return null;

        return $this->entries[$offset];
    }

    /**
     * Verify if the offset exists inside collection
     * 
     * @param mixed $offset Index inside collection
     * 
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->entries[$offset]);
    }

    /**
     * Gets the array iterator for entries.
     * 
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entries);
    }

    /**
     * Counts how many entries this collection have
     * 
     * @return int
     */
    public function count()
    {
        return count($this->entries);
    }

    /**
     * Applies a search inside this collection
     * 
     * @param callable $callback Function comparator
     * 
     * @return GrfEntryCollection
     */
    public function where($callback)
    {
        // No callable function? Then, no result. =]
        if (is_callable($callback) === false)
            return null;

        $tmpEntries = [];
        foreach ($this as $entry) {
            if ($callback($entry) === true)
                $tmpEntries[$entry->getFilename()] = $entry;
        }

        return new GrfEntryCollection($tmpEntries);
    }

    /**
     * Gets the first GrfEntryHeader in the collection
     * 
     * @param callable $callback Function comparator
     * 
     * @return GrfEntryHeader
     */
    public function first($callback = null)
    {
        $collection = $this;
        
        if ($callback !== null && is_callable($callback))
            $collection = $this->where($callback);

        $tmpArray = $collection->getArrayCopy();
        return array_shift($tmpArray);
    }

    /**
     * Gets the last entry on collection
     * 
     * @param callable $callback Function comparer
     * 
     * @return GrfEntryHeader
     */
    public function last($callback = null)
    {
        $collection = $this;
        
        if ($callback !== null && is_callable($callback))
            $collection = $this->where($callback);

        $tmpArray = $collection->getArrayCopy();
        return array_pop($tmpArray);
    }

    /**
     * Gets an copy from array entries
     * 
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->entries;
    }
}