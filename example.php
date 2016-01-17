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

print_r($filelist);


// List files using file() method with a wildcard pattern

$filelist = (new FileSystemFinder('C:/php/ext'))
    ->file('php_pdo_*.dll')
    ->results();

print_r($filelist);


// List files using dir() and file() method with wildcard and regex patterns

$filelist = (new FileSystemFinder('C:/php'))
    ->dir('dev|ext')                                    // using default wildcard matcher
    ->file('/[0-9]/', FileSystemFinder::REGEX_MATCHER)  // using the specified regex matcher
    ->results();

print_r($filelist);


// List files using wfio extension

if (extension_loaded('wfio')) {
    $filelist = FileSystemFinder::find('wfio://E:/Music/* 笑话/* 欢乐剧场/??? *大*.wma');

    print_r($filelist);
} else {
    echo "The wfio extension is not loaded.\r\n";
}

?>
</pre>
</body>
</html>
