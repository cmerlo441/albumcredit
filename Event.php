<?php

$no_header = 1;
require_once( './header.inc' );

class Event {
    private $year;
    private $month = 0;
    private $date = 0;
    private $message;
    private $sort_field;

    function __construct( $year, $month, $date, $message, $sort_field ) {
        $this->year = $year;
        $this->month = $month;
        $this->date = $date;
        $this->message = $message;
        $this->sort_field = $sort_field;
    }

    public function getYear() {
        return $this->year;
    }

    public function getMonth() {
        return $this->month;
    }

    public function getDay() {
        return $this->date;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getSortableDateString() {
        $message = "$this->year-";
        if( $this->month < 10 )
            $message .= '0';
        $message .= ( $this->month * 1 ) . '-';
        if( $this->date < 10 )
            $message .= "0";
        $message .= ( $this->date * 1 );
        return $message . $this->sort_field;
    }
}