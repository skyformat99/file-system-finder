<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FileSystemFinder Example</title>
</head>
<body>
<pre>
<?php
include_once 'FileSystemFinder.php';

// List files using static method FileSystemFinder::find()

$filelist = FileSystemFinder::find('C:/php/ext/php_pdo_*.dll');

print_r($filelist);                 // via __debugInfo()
echo "\r\n";


// List files using file() method with a wildcard pattern

$filelist = (new FileSystemFinder('C:/php/ext'))
    ->file('php_pdo_*.dll');

print_r($filelist->toArray());      // using toArray()
echo "\r\n";


// List files using dir() and file() method with wildcard and regex patterns

$filelist = (new FileSystemFinder('C:/php'))
    ->dir('dev|ext')                                    // using default wildcard matcher
    ->file('/[0-9]/', FileSystemFinder::REGEX_MATCHER); // using the specified regex matcher

foreach ($filelist as $path) {      // via SeekableIterator interface
    echo "$path\r\n";
}
echo "\r\n";


// A combination of using both static and non-static method

$filelist = FileSystemFinder::find('C:/php/dev|ext', FileSystemFinder::DIR_ONLY);
print_r($filelist);

$filelist = $filelist->file('/[0-9]/', FileSystemFinder::REGEX_MATCHER);
print_r($filelist);

echo "\r\n";


// List files using wfio extension

if (extension_loaded('wfio')) {
    $filelist = FileSystemFinder::find('wfio://E:/Music/* 笑话/* 欢乐剧场/??? *大*.wma');

    for ($i = 0; $i < count($filelist); $i++) {     // via Countable interface
        echo "[$i] => $filelist[$i]\r\n";           // via ArrayAccess interface
    }
} else {
    echo "The wfio extension is not loaded.\r\n";
}

?>
</pre>
</body>
</html>
