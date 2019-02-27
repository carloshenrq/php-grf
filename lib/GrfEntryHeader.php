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

class GrfEntryHeader
{
    /**
     * Gets the grf father about this entry
     * 
     * @return GrfFile
     */
    private $grf;

    /**
     * Entry file name
     * 
     * @var string
     */
    private $filename;

    /**
     * Compressed size entry
     * 
     * @return int
     */
    private $compressedSize;

    /**
     * Compressed size aligned
     * 
     * @return int
     */
    private $compressedSizeAligned;

    /**
     * Uncompressed size for entry
     * 
     * @return int
     */
    private $unCompressedSize;

    /**
     * Flags (?)
     * 
     * @return int
     */
    private $flags;

    /**
     * Offset position in grf file.
     * 
     * @return int
     */
    private $offset;

    /**
     * Header length.
     * @return int
     */
    private $size;

    /**
     * File bytes compressed state
     * 
     * @var string
     */
    private $compressedBytes;

    /**
     * Reads the buffer and populates the file entries
     * 
     * @param string       $name      Filename
     * @param BufferReader $buffer    Bytes to populate the header entry
     * @param GrfFile      $grf       The grf file who handles this file
     * @param string       $fileBytes File bytes content
     * 
     * @return void
     */
    public function __construct($name, BufferReader $buffer = null, GrfFile $grf, $fileBytes = null)
    {
        $this->grf = $grf;
        $this->size = 17; // Fixed
        $this->filename = utf8_encode($name);

        if ($fileBytes === null) {
            $this->compressedSize = $buffer->getUInt32();
            $this->compressedSizeAligned = $buffer->getUInt32();
            $this->unCompressedSize = $buffer->getUInt32();
            $this->flags = $buffer->getUInt8();
            $this->offset = $buffer->getUInt32();
            $this->grf->setLastOffsetFile($this->offset);
            $this->offset += GrfFile::GRF_HEADER_SIZE;
            return;
        }

        // New files must to calculate they position
        $this->compressedBytes = $this->grf->compress($fileBytes);
        $this->compressedSize = strlen($this->compressedBytes);
        $this->compressedSizeAligned = $this->compressedSize + (4 - (($this->compressedSize - 1) % 4)) - 1;
        $this->unCompressedSize = strlen($fileBytes);
        $this->flags = 1;
        $this->offset = $this->grf->getLastOffsetFile();
        $this->grf->setLastOffsetFile($this->offset);
        $this->offset += GrfFile::GRF_HEADER_SIZE;
    }

    /**
     * Gets the hash for the file
     * 
     * @param string $algo Algorithm that'll hash the files
     * 
     * @return string
     */
    public function getHash($algo = 'md5')
    {
        $buffer = $this->getCompressedBuffer();
        $hash = hash($algo, $buffer);
        unset ($buffer);
        return $hash;
    }

    /**
     * Fetches the uncompressed buffer for this entry in grf file
     * 
     * @return string
     */
    public function getUnCompressedBuffer()
    {
        return $this->grf->decompress($this->getCompressedBuffer());
    }

    /**
     * Fetchs the compressed buffer for this entry in grf file
     * 
     * @return string
     */
    public function getCompressedBuffer()
    {
        if ($this->compressedBytes === null) {
            $this->compressedBytes = $this->getGrf()->readBuffer($this->getOffset(), $this->getCompressedSize());
            return $this->getCompressedBuffer();
        }

        return $this->compressedBytes;
    }

    /**
     * Returns the grf father for this entry
     * 
     * @return GrfFile
     */
    public function getGrf()
    {
        return $this->grf;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Gets the offset position file in grf
     * 
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get the flag for entry
     * 
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Gets the uncompressed size for the entry
     * 
     * @return int
     */
    public function getUnCompressedSize()
    {
        return $this->unCompressedSize;
    }

    /**
     * Gets the compressed size aligned for the entry
     * 
     * @return int
     */
    public function getCompressedSizeAligned()
    {
        return $this->compressedSizeAligned;
    }

    /**
     * Gets the compressed size for the entry
     * 
     * @return int
     */
    public function getCompressedSize()
    {
        return $this->compressedSize;
    }

    /**
     * Gets entry file name
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Full header length
     * 
     * @return int
     */
    public function getHeaderLength()
    {
        return $this->size;
    }

    /**
     * Gets the table header buffer
     * 
     * @return string
     */
    public function getTableHeader()
    {
        $buffer = new BufferWriter();

        $buffer->appendString($this->getFilename());
        $buffer->appendUInt8(0);
        $buffer->appendUInt32($this->getCompressedSize());
        $buffer->appendUInt32($this->getCompressedSizeAligned());
        $buffer->appendUInt32($this->getUnCompressedSize());
        $buffer->appendUInt8(1);
        $buffer->appendUInt32($this->getOffset() - GrfFile::GRF_HEADER_SIZE);

        return $buffer->flush();
    }
}
