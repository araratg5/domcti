<?php
//sessionstart
session_start();
//login system
unset($_SESSION['id']);
unset($_SESSION['pass']);
header("Location:". "/");