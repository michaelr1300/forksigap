<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Mike42\Escpos\Printer; 
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Escpos extends Escpos
{
    public function __construct()
    {
        parent::__construct();
    }
}