# PHP-GRF [![release](https://img.shields.io/github/release/carloshenrq/php-grf.svg)](https://github.com/carloshenrq/php-grf/releases) [![license](https://img.shields.io/github/license/carloshenrq/php-grf.svg)](https://github.com/carloshenrq/php-grf)

This is a lib to allow PHP to read and write GRF (Gravity Ragnarok Files).

### Build Status

[![Build Status](https://travis-ci.com/carloshenrq/php-grf.svg?branch=master)](https://travis-ci.com/carloshenrq/php-grf) [![Build status](https://ci.appveyor.com/api/projects/status/pgw1am9vx6i7lhqk?svg=true)](https://ci.appveyor.com/project/carloshenrq/php-grf) [![issues](https://img.shields.io/github/issues/carloshenrq/php-grf.svg)](https://github.com/carloshenrq/php-grf/issues) [![pull-request](https://img.shields.io/github/issues-pr/carloshenrq/php-grf.svg)](https://github.com/carloshenrq/php-grf/pulls)

### Code Quality

[![CodeFactor](https://www.codefactor.io/repository/github/carloshenrq/php-grf/badge/master)](https://www.codefactor.io/repository/github/carloshenrq/php-grf/overview/master) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/791bcc480eac42cb937183daf5b827ae)](https://www.codacy.com/app/carloshenrq/php-grf?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=carloshenrq/php-grf&amp;utm_campaign=Badge_Grade)

### Code Coverage

[![codecov](https://codecov.io/gh/carloshenrq/php-grf/branch/master/graph/badge.svg)](https://codecov.io/gh/carloshenrq/php-grf) [![Codacy Badge](https://api.codacy.com/project/badge/Coverage/791bcc480eac42cb937183daf5b827ae)](https://www.codacy.com/app/carloshenrq/php-grf?utm_source=github.com&utm_medium=referral&utm_content=carloshenrq/php-grf&utm_campaign=Badge_Coverage)

## How i can colaborate?

Please! check our  and if you can fix or add new features, you're welcome.

Also, see if what you're about to change is in ours .

## How to open a file to read?

The code ahead, will show you how to extract all files inside grf.

```
<?php
require_once 'php-grf/lib/autoload.php';

// Instance a reader/writer for your grf file
$grf = new GrfFile('php-grf/tests/test200.grf');

foreach ($grf->getEntries() as $entry) {
    $dir = dirname($entry->getFilename());

    if (is_dir($dir) === false)
        mkdir ($dir, 0777, true);

    $file = str_replace('\\', '/', $entry->getFilename());
    $buffer = $entry->getUnCompressedBuffer();

    $fp = fopen($file, 'wb');
    fwrite($fp, $buffer);
    fflush($fp);
    fclose($fp);
}

// Dispose all resources used
$grf = null;
```

## How to open a grf file to write?

The same way you open to read, you can write. See:

```
require_once 'php-grf/lib/autoload.php';

// Instance a reader/writer for your grf file
$grf = new GrfFile('php-grf/tests/test200.grf');

$grf->addFile('file-outside.txt', 'data\\file-inside.txt');

// Dispose all resources used
$grf->close(); // Auto save when closed
$grf = null;
```

When you call `$grf->save()` or `$grf->close()` this'll repack your grf.
Large grf files may take some minutes to finish.

## How to create a grf file to write?

```
Soon...
```

## Install

To install, you only need use composer require to use.

```
composer require carloshlfz/php-grf
```

Just make sure you've `vendor/autoload.php` loaded and is just start to use.

## Credits and other infos

The grf file inside tests folder `test200.grf` is from [arminherling/GRF](https://github.com/arminherling/GRF/).

This code is based on [MagicalTux/grf](https://github.com/MagicalTux/grf)
