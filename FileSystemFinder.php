<?php
/**
 * FileSystemFinder is a PHP library that can list files and directories hierarchically
 * using wildcard and regular expression patterns.
 *
 * PHP version 5
 *
 * @author      Wudi <wudi@wudilabs.org>
 * @copyright   2016 Wudi
 * @license     https://opensource.org/licenses/MIT  The MIT License
 * @link        http://www.wudilabs.org/
 * @link        https://github.com/wudicgi/file-system-finder
 */

class FileSystemFinder implements SeekableIterator, Countable, ArrayAccess {
    const WILDCARD_MATCHER = 'WildcardMatcher';
    const REGEX_MATCHER = 'RegexMatcher';

    const ALL_TARGET = 0;
    const DIR_ONLY = 1;
    const FILE_ONLY = 2;

    private $_default_matcher = self::WILDCARD_MATCHER;

    private $_results = array();

    private $_position = 0;

    // {{{ find()

    /**
     * Finds all directories or files that match the specified expression
     *
     * @param string    $expr       The expression
     */
    public static function find($expr, $target_type = self::ALL_TARGET) {
        $parts = explode('/', $expr);

        $base_path = array_shift($parts);

        while (count($parts)) {
            if (strpos($parts[0], '*') === false
                && strpos($parts[0], '?') === false
                && strpos($parts[0], '|') === false) {
                $base_path .= '/' . array_shift($parts);
            } else {
                break;
            }
        }

        $Finder = new FileSystemFinder($base_path);

        if (!count($parts)) {
            return $Finder;
        }

        while (count($parts) >= 2) {
            $Finder = $Finder->dir(array_shift($parts));
        }

        if ($target_type == self::ALL_TARGET) {
            $Finder = $Finder->all($parts[0]);
        } elseif ($target_type == self::DIR_ONLY) {
            $Finder = $Finder->dir($parts[0]);
        } elseif ($target_type == self::FILE_ONLY) {
            $Finder = $Finder->file($parts[0]);
        }

        return $Finder;
    }

    // }}}

    // {{{ constructor

    /**
     * The constructor
     *
     * @param string    $directory      The initial directory
     * @param integer   $matcher        The default match type for directory and file name
     */
    function __construct($directory, $matcher = self::WILDCARD_MATCHER) {
        $this->_results = array($directory);
        $this->_default_matcher = $matcher;
    }

    // }}}

    public function all($expr, $matcher = null) {
        $this->_internalScan($expr, $matcher, self::ALL_TARGET);

        return $this;
    }

    public function dir($expr, $matcher = null) {
        $this->_internalScan($expr, $matcher, self::DIR_ONLY);

        return $this;
    }

    public function file($expr, $matcher = null) {
        $this->_internalScan($expr, $matcher, self::FILE_ONLY);

        return $this;
    }

    public function toArray() {
        return $this->_results;
    }

    private function _internalScan($expr, $matcher, $target_type) {
        $matcher_instance = $this->_getMatcherInstance($expr, $matcher);

        $new_results = array();

        foreach ($this->_results as $path) {
            $list = $this->_scandirWithTargetType($path, $target_type);

            foreach ($list as $new_item) {
                if ($matcher_instance->match($new_item)) {
                    $new_results[] = "$path/$new_item";
                }
            }
        }

        $this->_results = $new_results;
    }

    private function _getMatcherInstance($expr, $matcher) {
        if (is_null($matcher)) {
            $matcher = $this->_default_matcher;
        }

        if (is_string($matcher)) {
            if ($matcher == self::WILDCARD_MATCHER) {
                $instance = new WildcardMatcher($expr);
            } elseif ($matcher == self::REGEX_MATCHER) {
                $instance = new RegexMatcher($expr);
            } elseif (is_subclass_of($matcher, 'ExpressionMatcher')) {
                $instance = new $matcher($expr);
            } else {
                if (class_exists($matcher)) {
                    throw new Exception("Class '$matcher' did not implement the ExpressionMatcher interface.");
                } else {
                    throw new Exception("Class '$matcher' does not exist.");
                }
            }
        } elseif (is_object($matcher)) {
            if (is_subclass_of($matcher, 'ExpressionMatcher')) {
                $instance = $matcher;
            } else {
                throw new Exception("The given $matcher did not implement the ExpressionMatcher interface.");
            }
        } else {
            throw new Exception("The specified $matcher must be a string or ExpressionMatcher instance.");
        }

        return $instance;
    }

    private function _scandirWithTargetType($path, $target_type) {
        $list = scandir($path);

        $items = array();

        foreach ($list as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if ($target_type == self::ALL_TARGET) {
                $items[] = $item;
            } elseif (($target_type == self::DIR_ONLY) && is_dir("$path/$item")) {
                $items[] = $item;
            } elseif (($target_type == self::FILE_ONLY) && is_file("$path/$item")) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function __debugInfo() {
        return $this->toArray();
    }

    // SeekableIterator

    public function current() {
        return $this->_results[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        $this->_position++;
    }

    public function rewind() {
        $this->_position = 0;
    }

    public function valid() {
        return array_key_exists($this->_position, $this->_results);
    }

    public function seek($position) {
        if (!array_key_exists($position, $this->_results)) {
            trigger_error('The given position is out of bounds', E_USER_NOTICE);
        }

        $this->_position = $position;
    }

    // Countable

    public function count() {
        return count($this->_results);
    }

    // ArrayAccess

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->_results);
    }

    public function offsetGet($offset) {
        return $this->_results[$offset];
    }

    public function offsetSet($offset, $value) {
        trigger_error('The ' . __CLASS__ . ' object cannot be modified', E_USER_NOTICE);
    }

    public function offsetUnset($offset) {
        trigger_error('The ' . __CLASS__ . ' object cannot be modified', E_USER_NOTICE);
    }
}

interface ExpressionMatcher {
    function __construct($pattern);

    public function match($subject);
}

class WildcardMatcher implements ExpressionMatcher {
    private $_patterns;

    function __construct($pattern) {
        if (strpos($pattern, '|') !== false) {
            $this->_patterns = explode('|', $pattern);
        } else {
            $this->_patterns = array($pattern);
        }
    }

    public function match($subject) {
        if (is_array($this->_patterns)) {
            foreach ($this->_patterns as $pattern) {
                if (fnmatch($pattern, $subject)) {
                    return true;
                }
            }

            return false;
        } else {
            return fnmatch($this->_patterns, $subject);
        }
    }
}

class RegexMatcher implements ExpressionMatcher {
    private $_pattern;

    function __construct($pattern) {
        $this->_pattern = $pattern;
    }

    public function match($subject) {
        return preg_match($this->_pattern, $subject);
    }
}

?>
