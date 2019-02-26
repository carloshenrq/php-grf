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

class BufferReaderTest extends PHPUnit\Framework\TestCase
{
    private $buffer;

    public function setUp()
    {
        $writer = new BufferWriter();
        $writer->appendUInt16(0xfff8);
        $writer->appendInt32(-0xfffff);
        $writer->appendUInt32(0xfffff);
        $writer->appendInt8(-120);
        $writer->appendUInt8(255);
        $writer->appendInt16(0x7fff);
        $writer->appendString(GrfFile::GRF_HEADER_MAGIC);
        $this->buffer = $writer->flush();
    }

    public function testFullReader()
    {
        $reader = new BufferReader($this->buffer);

        $uint16 = $reader->getUInt16();
        $this->assertEquals(0xfff8, $uint16);

        $int32 = $reader->getInt32();
        $this->assertEquals(-0xfffff, $int32);

        $uint32 = $reader->getUInt32();
        $this->assertEquals(0xfffff, $uint32);

        $int8 = $reader->getInt8();
        $this->assertEquals(-120, $int8);

        $uint8 = $reader->getUInt8();
        $this->assertEquals(255, $uint8);

        $int16 = $reader->getInt16();
        $this->assertEquals(0x7fff, $int16);

        $str = $reader->getString(15);
        $this->assertEquals($str, GrfFile::GRF_HEADER_MAGIC);
    }
}
