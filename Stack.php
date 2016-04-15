<?php

class Stack {
    private $data;
    private $size;

    public function __construct() {
        $this->data = array();
        $this->size = 0;
    }

    public function size() {
        return $this->size;
    }

    public function isEmpty() {
        return $this->size == 0;
    }

    public function push( $data ) {
        $this->data[ $this->size++ ] = $data;
    }

    public function pop() {
        if( $this->size > 0 )
            return $this->data[ --$this->size ];
    }

    public function __toString() {
        $returnMe = '';
        for( $i = 0; $i < $this->size; $i++ ) {
            $returnMe .= $this->data[ $i ];
        }
        return $returnMe;
    }
}

?>