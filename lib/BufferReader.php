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

class BufferReader
{
    /**
     * Buffer content.
     * 
     * @var string
     */
    private $buffer;

    /**
     * Buffer position reader.
     * 
     * @var int
     */
    private $pos;

    /**
     * Constructor for buffer reader
     * 
     * @param string $buffer byte array to be read.
     * @param int    $pos    Start reading position
     * 
     * @return void
     */
    public function __construct($buffer, $pos = 0)
    {
        $this->buffer = $buffer;
        $this->pos = $pos;
    }

    /**
     * Destructs the current buffer reader
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->buffer = null;
        $this->pos = 0;
    }

    /**
     * Gets the full length of buffer
     * 
     * @return int
     */
    public function getLength()
    {
        return strlen($this->buffer);
    }

    /**
     * Sets the current position of reading buffer
     * 
     * @param int $pos The position to be setted
     * 
     * @return void
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    /**
     * Gets the current position of reading buffer
     * 
     * @return int
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Fetches the string inside buffer.
     * 
     * @param int $len the len to be readed
     * 
     * @return string
     */
    public function getString($len)
    {
        $strBuffer = substr($this->buffer, $this->pos, $len);
        $this->pos += $len;
        return trim($strBuffer);
    }

    /**
     * Fetches one signed byte from buffer.
     * 
     * @return int
     */
    public function getInt8()
    {
        $int8 = unpack('c', substr($this->buffer, $this->pos));
        $this->pos += 1;
        return $int8[1];
    }

    /**
     * Fetches one unsigned byte from buffer
     * 
     * @return int
     */
    public function getUInt8()
    {
        $uint8 = unpack('C', substr($this->buffer, $this->pos));
        $this->pos += 1;
        return $uint8[1];
    }

    /**
     * Fetches an signed int16 from buffer.
     * 
     * @return int
     */
    public function getInt16()
    {
        $int16 = unpack('s', substr($this->buffer, $this->pos));
        $this->pos += 2;
        return $int16[1];
    }

    /**
     * Fetches an unsigned int16 from buffer
     * 
     * @return int
     */
    public function getUInt16()
    {
        $int16 = unpack('S', substr($this->buffer, $this->pos));
        $this->pos += 2;
        return $int16[1];
    }

    /**
     * Fetches an signed int from buffer
     * 
     * @return int
     */
    public function getInt32()
    {
        $int32 = unpack('l', substr($this->buffer, $this->pos));
        $this->pos += 4;
        return $int32[1];
    }

    /**
     * Fetches an unsigned int from buffer
     * 
     * @return int
     */
    public function getUInt32()
    {
        $uint32 = unpack('L', substr($this->buffer, $this->pos));
        $this->pos += 4;
        return $uint32[1];
    }
}