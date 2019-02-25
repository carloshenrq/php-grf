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
class GrfFileHeader
{
	/**
	 * Must be 'Master of Magic' 16 caracters.
	 *
	 * @var string
	 */
	private $headerMagic;

	/**
	 * Header key for this grf file.
	 *
	 * @var string
	 */
	private $headerKey;

	/**
	 * @var int
	 */
	private $headerOffset;

	/**
	 * @int
	 */
	private $headerSeed;

	/**
	 * @int
	 */
	private $headerFileCount;

	/**
	 * @int
	 */
	private $headerVersion;

	/**
	 * Gets the grf file header.
	 * 
	 * @param string $headerReader Full grf header file.
	 *
	 * @return void
	 */
	public function __construct($headerRead)
	{
		// Reads the header
		$this->headerMagic = trim(substr($headerRead, 0, 16));
		$this->headerKey = trim(substr($headerRead, 16, 14));
		$this->headerOffset = unpack('L', $headerRead, 30);
		$this->headerSeed = unpack('L', $headerRead, 34);
		$this->headerFileCount = unpack('L', $headerRead, 38);
		$this->headerVersion = unpack('L', $headerRead, 42);

		// Ajust file count size.
		// https://github.com/carloshenrq/grf/blob/master/src/grf.c#L700
		$this->headerFileCount -= ($this->headerSeed + 7);
	}

	/**
	 * Returns the magic header into grf
	 *
	 * @return string
	 */
	public function getMagic()
	{
		return $this->headerMagic;
	}

	/**
	 * Returns the key into the grf.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->headerKey;
	}

	/**
	 * Returns the file table offset
	 *
	 * @return int
	 */
	public function getOffset()
	{
		return $this->headerOffset;
	}

	/**
	 * Returns the seed that write into GRF
	 *
	 * @return int
	 */
	public function getSeed()
	{
		return $this->headerSeed;
	}

	/**
	 * Returns the file count into this GRF file.
	 *
	 * @return int
	 */
	public function getFileCount()
	{
		return $this->headerFileCount;
	}

	/**
	 * Returns the current version that GRF is written
	 *
	 * @return int
	 */
	public function getVersion()
	{
		return $this->headerVersion;
	}
}
