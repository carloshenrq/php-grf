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
	 * Constructor for reading grf file
	 *
	 * @param string $fileName Name of the archive
	 *
	 * @return void
	 */
	public function __construct($fileName)
	{
		// The file doesn't exists?
		if (file_exists($fileName) === false)
			throw new GrfFileNotFoundException($fileName);

		$this->fileName = $fileName;
		$this->ptrFile = fopen($this->fileName, 'rb+');

		// Reads this grf header
		$this->readHeader();
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
		$headerRead = fread($this->ptrFile, GrfFile::GRF_HEADER_SIZE);

		// Set the header parser
		$this->header = new GrfFileHeader($headerRead);
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
	 * Closes the grf file handler
	 *
	 * @return void
	 */
	public function close()
	{
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
