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

class GrfFileTest extends PHPUnit\Framework\TestCase
{
    private $grf;

    public function setUp()
    {
        $this->grf = new GrfFile('tests/test200.grf');
    }

    public function testEntriesCollection()
    {
        $entries = $this->grf->getEntries();

        $this->assertNull($entries['data\\xampso.xml']);
        unset($entries['data\\xampso.xml']);
        $this->assertNull($entries['data\\xampso.xml']);

        $entry = $entries->first();
        $name = $entry->getFilename();
        $this->assertNotNull($entries[$name]);
        $entry->delete();
        $this->assertNull($entries[$name]);

        $nullWhere = $entries->where('xamspoTest');
        $this->assertNull($nullWhere);

        $otherEntries = $entries->where(function($e) use ($name) {
            return strcmp($e->getFilename(), $name) !== 0;
        });

        $this->assertEquals(8, $otherEntries->count());

        $first = $entries->first(function($e) use ($name) {
            return strcmp($e->getFilename(), $name) !== 0;
        });

        $this->assertInstanceOf('GrfEntryHeader', $first);

        $last = $entries->last();
        $this->assertInstanceOf('GrfEntryHeader', $last);

        $last = $entries->last(function($e) use ($name) {
            return strcmp($e->getFilename(), $name) !== 0;
        });

        $this->assertInstanceOf('GrfEntryHeader', $last);
    }

    public function testEntriesCount()
    {
        $entries = $this->grf->getEntries();

        $this->assertEquals(9, $entries->count());

        $grfEntry = $entries->first();
        $this->assertInstanceOf('GrfEntryHeader', $grfEntry);

        $this->assertEquals('data\\0_Tex1.bmp', $grfEntry->getFilename());
        $this->assertEquals(52900, $grfEntry->getCompressedSize());
        $this->assertEquals(52900, $grfEntry->getCompressedSizeAligned());
        $this->assertEquals(196664, $grfEntry->getUnCompressedSize());
        $this->assertEquals(1, $grfEntry->getFlags());
        $this->assertEquals(46, $grfEntry->getOffset());
        $this->assertEquals($this->grf, $grfEntry->getGrf());
        $this->assertEquals('829ce15b9b3c89baccdfbc4d7fe3d435', $grfEntry->getHash());

        $hashBuffer = hash('md5', $grfEntry->getUnCompressedBuffer());
        $hashFile = hash_file('md5', 'tests/0_Tex1.bmp');

        $this->assertEquals($hashFile, $hashBuffer);

        // AvString to test
        $this->assertEquals(15, $this->grf->getAvLen(GrfFile::GRF_HEADER_MAGIC, 15));
    }
}
