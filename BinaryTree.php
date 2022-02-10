<?php

class BinaryNode {
    public $position;
    public $children = array();
    public $parent;

    public function __construct($position){
        $this->position = $position;
    }
}

class BinaryTree{
    
}

$parent = new BinaryNode("1");
$child =  new BinaryNode("2");
$parent->children[] = $child;

echo "<pre>";
print_r($parent);
echo "</pre>";