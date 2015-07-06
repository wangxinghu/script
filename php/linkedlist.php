<?php
date_default_timezone_set('UTC');
class Node {
	public $data;
	public $next;
	public function __construct($data=null) {
		$this->data = $data;
		$this->next = null;
	}
}

class SingleLinkNode {
	public $header;
	public function __construct() {
		$this->header = null;
	}
	public function insertNode($data) {
		if ($this->header === null) {
			$this->header = new Node($data);
			return;
		}
		$curNode = $this->header;
		while($curNode->next !== null) {
			$curNode = $curNode->next;
		}
		$curNode->next = new Node($data);
	}
	public function reverse() {
		if ($this->header === null || $this->header->next === null) {
			return $this->header;
		}
		$before = $this->header;
		$cur = $before->next;
		$after = $cur->next;
		$before->next = null;
		$cur->next = $before;
		while ($after !== null) {
			$before = $cur;
			$cur = $after;
			$after = $after->next;
			$cur->next = $before;
		}
		$this->header = $cur;
	}
	public function output() {
		$cur = $this->header;
		while($cur !== null) {
			print_r($cur->data."\n");
			$cur = $cur->next;
		}
		print_r('end'."\n");
	}
}

$link = new SingleLinkNode();
$link->insertNode(2);
$link->insertNode(5);
$link->insertNode(7);
$link->insertNode(3);
$link->insertNode(4);
$link->insertNode(1);

$link->output();
$link->reverse();
$link->output();