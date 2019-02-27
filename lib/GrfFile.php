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

/**
 * This is the main class for reading the grf files.
 * Here, we'll get the header info for read and write files.
 */
class GrfFile
{
    /**
     * The grf filename.
     *
     * @var string
     */
    private $fileName;

    /**
     * Handles the file size.
     *
     * @var int
     */
    private $fileSize;

    /**
     * Gets the start of table offset
     * 
     * @var int
     */
    private $tableOffset;

    /**
     * File hander to grf
     *
     * @var resource
     */
    private $ptrFile;

    /**
     * Header information about grf.
     *
     * @var GrfFileHeader
     */
    private $header;

    /**
     * All entries in grf file.
     * 
     * @var array
     */
    private $entries;

    /**
     * Check if this grf file is needing to be saved.
     * 
     * @var bool
     */
    private $isNeedingSave;

    /**
     * Constructor for reading grf file
     *
     * @param string $fileName       Name of the archive
     * @param bool   $readFileTables This indicates if table files should be readed
     *
     * @return void
     */
    public function __construct($fileName, $readFileTables = true)
    {
        $this->load($fileName, $readFileTables);
    }

    /**
     * Performs a loading in grf file
     * 
     * @param string $fileName       Name of the archive
     * @param bool   $readFileTables This indicates if table files should be readed
     * 
     * @return void
     */
    private function load($fileName, $readFileTables)
    {
        // The file doesn't exists?
        if (file_exists($fileName) === false)
            throw new GrfFileNotFoundException($fileName);

        $this->fileName = $fileName;
        $this->ptrFile = fopen($this->fileName, 'rb+');

        $this->entries = new ArrayObject();

        // Reads this grf header
        $this->readHeader();

        if ($readFileTables)
            $this->readTableFiles();

        // No need to save when open
        $this->isNeedingSave = false;
    }

    /**
     * Performs a read in table files
     * 
     * @return void
     */
    private function readTableFiles()
    {
        // ??
        $posinfo = [];

        // places the reader pointer in start of table files.
        fseek($this->ptrFile, $this->getTableOffset(), SEEK_SET);

        // read 8 bytes about posinfo
        // https://github.com/carloshenrq/grf/blob/master/src/grf.c#L823
        $buffer = new BufferReader(fread($this->ptrFile, 8));
        $posinfo[0] = $buffer->getUInt32();
        $posinfo[1] = $buffer->getUInt32();
        $buffer = null;

        // Read table info
        $table_comp = fread($this->ptrFile, $posinfo[0]);
        $table = null;

        // get info about broken pos...
        $brokenpos = fread($this->ptrFile, 4);
        
        // decompress table-info data, can read file info with this
        $table = zlib_decode($table_comp);

        // Entries read, populates it to be in memory
        $pos = 0;
        $tbm_table = $table;
        $max_pos = $posinfo[1];
        while ($pos < $max_pos) {
            $av_len = $max_pos - $pos;
            $fn_len = $this->getAvLen($tbm_table, $av_len);

            $buffer = new BufferReader(substr($table, $pos, $fn_len));
            $filename = $buffer->getString($buffer->getLength());
            $pos += ($fn_len + 1);

            $entry = new GrfEntryHeader($filename, new BufferReader(substr($table, $pos)), $this);
            $pos += $entry->getHeaderLength();

            $this->entries[] = $entry;
            $tbm_table = substr($table, $pos);
        }
    }

    /**
     * Gets the file size string
     * 
     * @param string $str    File table
     * @param int    $maxLen max len to be readed
     * 
     * @return int
     */
    public function getAvLen($str, $maxLen)
    {
        for ($i = 0; $i < $maxLen; $i++) {
            if (ord($str[$i]) == NULL)
                return $i;
        }

        return $maxLen;
    }

    /**
     * This reader the grf header struct
     *
     * @see https://github.com/carloshenrq/grf/blob/master/includes/grf.h#L103-L110
     *
     * @return void
     */
    private function readHeader()
    {
        // Read the full header
        fseek($this->ptrFile, 0, SEEK_SET);
        $headerRead = fread($this->ptrFile, self::GRF_HEADER_SIZE);

        // Set the header parser
        $this->header = new GrfFileHeader(new BufferReader($headerRead));

        // Table files offset
        $this->tableOffset = $this->getHeader()->getOffset() + self::GRF_HEADER_SIZE;
    }

    /**
     * Get all entries in grf file.
     * 
     * @return ArrayObject
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Get start of tables files inside grf.
     * 
     * @return int
     */
    public function getTableOffset()
    {
        return $this->tableOffset;
    }

    /**
     * Calculates the full grf file size.
     *
     * @return int
     */
    public function getFullSize()
    {
        $last = ftell($this->ptrFile);
        fseek($this->ptrFile, 0, SEEK_END);
        $this->fileSize = ftell($this->ptrFile);
        fseek($this->ptrFile, $last, SEEK_SET);

        return $this->fileSize;
    }

    /**
     * Fetches the request offset and len
     * 
     * @param int $offset read offset
     * @param int $len    size of read
     * 
     * @return string
     */
    public function readBuffer($offset, $len)
    {
        $last = ftell($this->ptrFile);
        fseek($this->ptrFile, $offset, SEEK_SET);
        $buffer = fread($this->ptrFile, $len);
        fseek($this->ptrFile, $last, SEEK_SET);

        return $buffer;
    }

    /**
     * Add new fileinside grf
     * 
     * @param string $oname The file name outise grf
     * @param string $iname The file name inside grf
     * 
     * @return void
     */
    public function addFile($oname, $iname)
    {
        if (file_exists($oname) === false)
            return;

        $size = filesize($oname);
        $fpBuf = fopen($oname, 'rb');
        $buffer = fread($fpBuf, $size);
        fclose($fpBuf);

        $this->addBuffer($iname, $buffer);
    }

    /**
     * Add new named buffer in grf...
     * 
     * @param string $name   The name inside grf
     * @param string $buffer File contents
     * 
     * @return void
     */
    public function addBuffer($name, $buffer)
    {
        $name = str_replace('/', '\\', $name); // Replaces '/' by '\\'... Grf default separator
        $entry = new GrfEntryHeader($name, null, $this, $buffer);
        $this->entries[] = $entry;
        $this->isNeedingSave = true;
    }

    /**
     * Decompress the data
     * 
     * @param string $data Compressed data
     * 
     * @return string
     */
    public function decompress($data)
    {
        return zlib_decode($data);
    }

    /**
     * Compresses the data
     * 
     * @param string $data Data to be compressed
     * 
     * @return string
     */
    public function compress($data)
    {
        return zlib_encode($data, ZLIB_ENCODING_DEFLATE);
    }

    /**
     * Returns the file header readed.
     *
     * @return GrfFileHeader
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Object destructor, closes the file handler
     *
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Saves the file writing and packing all data inside grf.
     * This is a repack.
     * 
     * @return void
     */
    public function save()
    {
        $this->isNeedingSave = false;
        $header = $this->getHeader();
        $entries = $this->getEntries();
        $tableFilesOffset = 0;
        $tmpFile = $this->fileName . '.tmp';

        // Buffer writer
        $fpTmp = fopen($tmpFile, 'wb');

        foreach ($entries as $entry)
            $tableFilesOffset += $entry->getCompressedSizeAligned();

        $buf = new BufferWriter();
        $buf->appendString($header->getMagic());
        $buf->appendUInt8(0);
        $buf->appendString($header->getKey());
        $buf->appendUInt32($tableFilesOffset); // This resets the header... should be calculated again...
        $buf->appendUInt32(0);
        $buf->appendUInt32($entries->count() + 7);
        $buf->appendUInt32(0x200);

        fwrite($fpTmp, $buf->flush());
        fflush($fpTmp);

        $fileTableArray = new ArrayObject();
        $entryOffset = 0;
        foreach ($entries as $entry) {
            fseek($fpTmp, $entryOffset + self::GRF_HEADER_SIZE, SEEK_SET);

            $len = fwrite($fpTmp, $entry->getCompressedBuffer());
            $aligned = $entry->getCompressedSizeAligned();

            for ($i = $len; $i < $aligned; $i += fwrite($fpTmp, pack('c', 0)));

            fflush($fpTmp);

            // Append entry header in memory buffer
            $buf->appendString(utf8_decode($entry->getFilename()));
            $buf->appendUInt8(0);
            $buf->appendUInt32($len);
            $buf->appendUInt32($aligned);
            $buf->appendUInt32($entry->getUnCompressedSize());
            $buf->appendUInt8($entry->getFlags());
            $buf->appendUInt32($entryOffset);

            $entryOffset += $aligned;
        }

        // Calculates the size of table entries
        // flushes the current buffer
        $tEntriesLen = $buf->getLength();
        $tEntries = $buf->flush();
        $tEntriesCompress = $this->compress($tEntries);
        $tEntriesCompressLen = strlen($tEntriesCompress);

        // Appends the entries compressed length and the uncompressed
        // length
        $buf->appendUInt32($tEntriesCompressLen);
        $buf->appendUInt32($tEntriesLen);

        // Writes in the grf file the buffer table length
        // and compressed table length
        fwrite($fpTmp, $buf->flush());
        fwrite($fpTmp, $tEntriesCompress);
        fwrite($fpTmp, pack('L', 0));

        // Closes the tmp file
        fclose($fpTmp);
        $this->close();

        // override the old file by the new one
        unlink($this->fileName);
        rename($tmpFile, $this->fileName);

        // reloads the grf file
        $this->load($this->fileName, true);
    }

    /**
     * Closes the grf file handler
     *
     * @return void
     */
    public function close()
    {
        if ($this->isNeedingSave)
            $this->save();

        if ($this->ptrFile !== null) {
            fclose($this->ptrFile);
            $this->ptrFile = null;
        }
    }

    /**
     * Total header size.
     * 
     * @var int
     */
    const GRF_HEADER_SIZE = 0x2e;

    /**
     * The header magic data.
     *
     * @var string
     */
    const GRF_HEADER_MAGIC = "Master of Magic";
}
