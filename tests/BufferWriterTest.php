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

require_once 'lib/autoload.php';

class BufferWriterTest extends PHPUnit\Framework\TestCase
{
    private $wBuffer;

    public function setUp()
    {
        $this->wBuffer = new BufferWriter();
    }

    public function testAppendUInt32()
    {
        $this->wBuffer->appendUInt32(0x7fffffff);
        $buffer = $this->wBuffer->flush();

        $unpacked = unpack('L', $buffer)[1];

        $this->assertEquals(0x7fffffff, $unpacked);
    }

    public function testAppendInt32()
    {
        $this->wBuffer->appendInt32(-0xfffff);
        $buffer = $this->wBuffer->flush();

        $unpacked = unpack('l', $buffer)[1];

        $this->assertEquals(-0xfffff, $unpacked);
    }

    public function testAppendUInt16()
    {
        $this->wBuffer->appendUInt16(0xffff);
        $buffer = $this->wBuffer->flush();

        $unpacked = unpack('S', $buffer)[1];

        $this->assertEquals(0xffff, $unpacked);
    }

    public function testAppendInt16()
    {
        $this->wBuffer->appendInt16(0x7fff);
        $buffer = $this->wBuffer->flush();

        $unpacked = unpack('s', $buffer)[1];

        $this->assertEquals(0x7fff, $unpacked);
    }

    public function testAppendUInt8()
    {
        $this->wBuffer->appendUInt8(230);
        $buffer = $this->wBuffer->flush();

        $unpacked = unpack('C', $buffer)[1];

        $this->assertEquals($unpacked, 230);
    }

    public function testAppendInt8()
    {
        $this->wBuffer->appendInt8(-120);
        $buffer = $this->wBuffer->flush();

        $unpacked = unpack('c', $buffer)[1];

        $this->assertEquals($unpacked, -120);
    }

    public function testAppendString()
    {
        $this->wBuffer->appendString(GrfFile::GRF_HEADER_MAGIC);
        $buffer = $this->wBuffer->flush();

        $this->assertEquals($buffer, GrfFile::GRF_HEADER_MAGIC);
    }

    public function testGetLength()
    {
        $this->assertEquals(0, $this->wBuffer->getLength());
    }

    public function testDestructor()
    {
        $this->wBuffer = null;
        $this->assertNull($this->wBuffer);
    }
}
