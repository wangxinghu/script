<?php
class test {
    public function tt() {
        set_error_handler(array($this, "ignoreError"));
//        set_exception_handler(array($this, "ignoreError"));
        throw new Exception('aaa');
    }
    public function ignoreError($code, $error, $file = NULL, $line = NULL) {
        var_dump($code);
        var_dump($error);
        var_dump($file);
        var_dump($line);
        return true;
    }
}

$obj = new test();
$obj->tt();
var_dump('good');
