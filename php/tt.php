<?php
class Person {
    public $a = 1;
    public function add() {
        $this->a +=1;
    }
}
function add($p) {
    $p->add();
}
$p = new Person();
var_dump($p->a);
add($p);
var_dump($p->a);
exit;

