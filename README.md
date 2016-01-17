# FileSystemFinder

A PHP library that can list files and directories hierarchically using wildcard and regular expression patterns.

## Examples

```php
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
```

The above example will output:

```
Array
(
    [0] => C:/php/ext/php_pdo_firebird.dll
    [1] => C:/php/ext/php_pdo_mysql.dll
    [2] => C:/php/ext/php_pdo_oci.dll
    [3] => C:/php/ext/php_pdo_odbc.dll
    [4] => C:/php/ext/php_pdo_pgsql.dll
    [5] => C:/php/ext/php_pdo_sqlite.dll
)
Array
(
    [0] => C:/php/ext/php_pdo_firebird.dll
    [1] => C:/php/ext/php_pdo_mysql.dll
    [2] => C:/php/ext/php_pdo_oci.dll
    [3] => C:/php/ext/php_pdo_odbc.dll
    [4] => C:/php/ext/php_pdo_pgsql.dll
    [5] => C:/php/ext/php_pdo_sqlite.dll
)
Array
(
    [0] => C:/php/dev/php5ts.lib
    [1] => C:/php/ext/php_bz2.dll
    [2] => C:/php/ext/php_gd2.dll
    [3] => C:/php/ext/php_oci8_12c.dll
    [4] => C:/php/ext/php_sqlite3.dll
)
Array
(
    [0] => wfio://E:/Music/04 笑话/01 欢乐剧场/036 武大日记.wma
    [1] => wfio://E:/Music/04 笑话/01 欢乐剧场/087 大学趣闻.wma
    [2] => wfio://E:/Music/04 笑话/01 欢乐剧场/109 武大郎后传.wma
    [3] => wfio://E:/Music/04 笑话/01 欢乐剧场/117 孙大圣“评职”申请书.wma
    [4] => wfio://E:/Music/04 笑话/01 欢乐剧场/120 肖大明白.wma
    [5] => wfio://E:/Music/04 笑话/01 欢乐剧场/156 吃大户.wma
    [6] => wfio://E:/Music/04 笑话/01 欢乐剧场/160 说大道小.wma
    [7] => wfio://E:/Music/04 笑话/01 欢乐剧场/168 四大…….wma
    [8] => wfio://E:/Music/04 笑话/01 欢乐剧场/197 过大年.wma
)
```

## License

The MIT License (MIT)

Copyright (c) 2016 Wudi <wudi@wudilabs.org>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
