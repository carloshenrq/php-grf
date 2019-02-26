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

class BufferWriter
{
    /**
     * Gets the current buffer data
     * 
     * @return string
     */
    private $buffer;

    /**
     * Gets the current buffer length
     * 
     * @return int
     */
    private $length;

    /**
     * Creates a new buffer writer
     * 
     * 
     * @return void
     */
    public function __construct()
    {
        $this->buffer = '';
        $this->length = 0;
    }

    /**
     * Destructs the current buffer writer
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->buffer = null;
        $this->length = 0;
    }

    /**
     * Gets the current length of buffer
     * 
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Appends an string to the buffer
     * 
     * @param string $str The string to be appended
     * 
     * @return void
     */
    public function appendString($str)
    {
        $this->buffer .= $str;
        $this->length += strlen($str);
    }

    /**
     * Appends an signed int8 to the buffer
     * 
     * @param int $int The byte to be appended
     * 
     * @return void
     */
    public function appendInt8($int)
    {
        $this->buffer .= pack('c', $int);
        $this->length += 1;
    }

    /**
     * Appends an unsigned int8 to the buffer
     * 
     * @param int $int The byte to be appended
     * 
     * @return void
     */
    public function appendUInt8($int)
    {
        $this->buffer .= pack('C', $int);
        $this->length += 1;
    }

    /**
     * Appends an signed int16 to the buffer
     * 
     * @param int $int The int16 to be appended
     * 
     * @return void
     */
    public function appendInt16($int)
    {
        $this->buffer .= pack('s', $int);
        $this->length += 2;
    }

    /**
     * Appends an unsigned int16 to the buffer
     * 
     * @param int $int The uint16 to be appended
     * 
     * @return void
     */
    public function appendUInt16($int)
    {
        $this->buffer .= pack('S', $int);
        $this->length += 2;
    }

    /**
     * Appends an signed int to the buffer
     * 
     * @param int $int The int to be appended
     * 
     * @return void
     */
    public function appendInt32($int)
    {
        $this->buffer .= pack('l', $int);
        $this->length += 4;
    }

    /**
     * Appends an unsigned int to the buffer
     * 
     * @param int $int The uint to be appended
     * 
     * @return void
     */
    public function appendUInt32($int)
    {
        $this->buffer .= pack('L', $int);
        $this->length += 4;
    }

    /**
     * Fetches the current string buffer and clear from memory
     * 
     * @return string
     */
    public function flush()
    {
        $buffer = $this->__toString();
        $this->buffer = '';
        $this->length = 0;
        return $buffer;
    }

    /**
     * Gets the current string buffer
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->buffer;
    }
}
