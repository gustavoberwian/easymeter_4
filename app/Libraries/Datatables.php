<?php
include_once "Datatables/Datatables.php";
include_once "Datatables/DB/DatabaseInterface.php";
include_once "Datatables/DB/CodeigniterAdapter.php";

use Ozdemir\Datatables\Datatables as DT;
use Ozdemir\Datatables\DB\CodeigniterAdapter;

class Datatables extends DT {
    function __construct()
    {
        parent::__construct(new CodeigniterAdapter);
    }
} 