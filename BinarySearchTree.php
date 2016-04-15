<?php 

class BSTNode
{
	public $parent = null;
	public $left = null;
	public $right = null;
	public $data = null;
 
	public function __construct($data)
	{
		$this->data = $data;
	}
 
	public function __toString()
	{
		return $this->data;
	}
}
 
class BinarySearchTree
{
	protected $_root = null;
 
	protected function _insert(&$new, &$node)
	{
		if ($node == null) {
			$node = $new;
			return;
		}
 
		if ($new->data <= $node->data) {
			if ($node->left == null) {
				$node->left = $new;
				$new->parent = $node;
			} else {
				$this->_insert($new, $node->left);
			}
		} else {
			if ($node->right == null) {
				$node->right = $new;
				$new->parent = $node;
			} else {
				$this->_insert($new, $node->right);
			}
		}		
	}
 
	protected function _search(&$target, &$node)
	{
		if ($target == $node) {
			return 1;
		} else if ($target->data > $node->data && isset($node->right)) {
			return $this->_search($target, $node->right);
		} else if ($target->data <= $node->data && isset($node->left)) {
			return $this->_search($target, $node->left);
		}
 
		return 0;
	}
 
	public function insert($node)
	{
		$this->_insert($node, $this->_root);
	}
 
	public function search($item) 
	{
		return $this->_search($item, $this->_root);
	}
}

/*
$a = new BSTNode(3);
$b = new BSTNode(2);
$c = new BSTNode(4);
$d = new BSTNode(7);
$e = new BSTNode(6);
 
$t = new BinaryTree();
 
$t->insert($a);
$t->insert($b);
$t->insert($c);
$t->insert($d);
$t->insert($e);
 
echo $t->search($e);
*/
?>