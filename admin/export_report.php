<?php
require_once "../includes/initialize.php";

$report_csv = new Report;

$report_csv->export_csv();
?>