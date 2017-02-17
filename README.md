# jsobytes-php
php lib for jsobytes

Uses:

$filename is optional.
______________________

$File2jsobytes = new File2jsobytes($filepath, $filename);

$file = $File2jsobytes->getJSON();

-----------------------------------------------------------------

$jsobytes is jsobytes.
______________________

$jsobytes2File = new jsobytes2File($jsobytes);

$file = $jsobytes2File->getFile();
