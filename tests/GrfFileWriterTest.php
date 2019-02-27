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

class GrfFileWriterTest extends PHPUnit\Framework\TestCase
{
    public function testGrfAddFile()
    {
        if (file_exists('tests/tmp_test200add.grf') === true)
            unlink('tests/tmp_test200add.grf');

        copy('tests/test200.grf', 'tests/tmp_test200add.grf');

        // Adds new file inside grf.
        $grf = new GrfFile('tests/tmp_test200add.grf');
        $grf->addFile('phpunit.xml', 'data\\phpunit.xml');
        $grf->close();
        $grf = null;

        $grf = new GrfFile('tests/tmp_test200add.grf');
        foreach ($grf->getEntries() as $entry) {
            if (strcmp($entry->getFilename(), 'data\\phpunit.xml') == 0) {
                $buffer = $entry->getUnCompressedBuffer();
                $fileHash = hash_file('md5', 'phpunit.xml');
                $buffHash = hash('md5', $buffer);

                $this->assertEquals($fileHash, $buffHash);
                break;
            }
        }

        $grf->addFile('non-existent-file.txt', 'data\\non-existent-file.txt');
        $grf->save();

        $e = null;
        foreach ($grf->getEntries() as $entry) {
            if (strcmp($entry->getFilename(), 'data\\non-existent-file.txt') == 0) {
                $e = $entry;
                break;
            }
        }

        // Can't find unexistent file.
        $this->assertNull($e);

        $grf->close();
        unlink('tests/tmp_test200add.grf');
    }

    public function testGrfRepack()
    {
        if (file_exists('tests/tmp_test200repack.grf') === true)
            unlink('tests/tmp_test200repack.grf');

        copy('tests/test200.grf', 'tests/tmp_test200repack.grf');

        $grf = new GrfFile('tests/tmp_test200repack.grf');

        // before repack
        $this->assertEquals(936658, $grf->getFullsize());

        // saves the grf and recalculate the size
        $grf->save();

        $this->assertEquals(858333, $grf->getFullsize());

        $grf->close();
        unlink('tests/tmp_test200repack.grf');
    }
}
