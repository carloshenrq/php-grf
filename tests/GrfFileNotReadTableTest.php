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

class GrfFileNotReadTableTest extends PHPUnit\Framework\TestCase
{
    /**
     * The grf handler for testing
     * 
     * @var GrfFile
     */
    private $grf;

    public function setUp()
    {
        $this->grf = new GrfFile('tests/test200.grf', false);
    }

    /**
     * Verify the full size from test200.grf
     */
    public function testGetFullSize()
    {
        $this->assertEquals($this->grf->getFullSize(), 936658);
    }

    /**
     * Test the entries info
     */
    public function testGetEntries()
    {
        $entries = $this->grf->getEntries();

        $this->assertInstanceOf('GrfEntryCollection', $entries);
        $this->assertEquals(0, $entries->count());
    }

    /**
     * Test the table files offset
     */
    public function testGetTableOffset()
    {
        $tableOffset = $this->grf->getTableOffset();
        
        $this->assertNotEquals(0, $tableOffset);
        $this->assertEquals(78730, $tableOffset);
    }

    /**
     * Test the destructor
     */
    public function testDestructor()
    {
        $this->grf = null;
        $this->assertNull($this->grf);
    }

    public function testGrfFileHeader()
    {
        $header = $this->grf->getHeader();

        $this->assertNotNull($header);
        $this->assertInstanceOf('GrfFileHeader', $header);

        $this->assertEquals($header->getMagic(), GrfFile::GRF_HEADER_MAGIC);
        $this->assertEquals(strlen($header->getKey()), 14);
        $this->assertEquals(0, $header->getSeed());
        $this->assertEquals(9, $header->getFileCount());
        $this->assertEquals(0x200, $header->getVersion());
    }
}
