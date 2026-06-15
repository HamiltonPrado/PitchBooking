<?php
session_start();
require 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];