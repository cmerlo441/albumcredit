<?php

class QueueNode {
    private $data;
    private $next;
    
    public function __construct( $data ) {
        $this->data = $data;
        $this->next = null;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function getNext() {
        return $this->next;
    }
    
    public function setNext( $data ) {
        $this->next = $data;
    }
    
    public function __toString() {
        return "$data";
    }
}

class Queue {
    private $head;
    private $tail;
    private $count;
    
    public function __construct() {
        $this->head = null;
        $this->tail = null;
        $this->count = 0;
    }
    
    public function size() {
        return $this->count;
    }
    
    public function isEmpty() {
        return $this->head == null;
    }
    
    public function enqueue( $data ) {
        $oldtail = $this->tail;
        $this->tail = new QueueNode( $data );
        if( $this->isEmpty() )
            $this->head = $this->tail;
        else
            $oldtail->setNext( $this->tail );
        ++$this->count;
    }
    
    public function peek() {
        return $this->head->getData();
    }
    
    public function dequeue() {
        $data = $this->head->getData();
        $this->head = $this->head->getNext();
        --$this->count;
        if( $this->isEmpty() )
            $this->tail = null;
        return $data;
    }
    
    public function __toString() {
        $returnMe = '';
        $node = $head;
        while( $node != null ) {
            $returnMe .= $node->data;
            $node = $node->next;
        }
        return $returnMe;
    }
}

?>