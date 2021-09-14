<?php

// $image = \tt\image\Barcode::CustomerBarcode('160005','２丁目３４−５')->write(\ebi\WorkingStorage::tmpfile('', '.png'));
// $image->write($path);
// eq(is_file($path));

$path = tempnam(sys_get_temp_dir(), 'test');

$path = \tt\image\Barcode::NW7('a1234567890123a')->write(tempnam(sys_get_temp_dir(), ''));
eq(is_file($path));


$path = \tt\image\Barcode::EAN13('4549995186550')->write(tempnam(sys_get_temp_dir(), ''));
eq(is_file($path));


$path = \tt\image\Barcode::CODE39('a1234567890123a')->write(tempnam(sys_get_temp_dir(), ''));
eq(is_file($path));
