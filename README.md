# jsobytes-php
php lib for jsobytes

Uses:

$filename is optional.


$File2jsobytes = new File2jsobytes($filepath, $filename);

$file = $File2jsobytes->getJSON();


$jsobytes is jsobytes.


$jsobytes2File = new jsobytes2File($jsobytes);

$file = $jsobytes2File->getFile();
