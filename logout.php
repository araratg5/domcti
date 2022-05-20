<?php
//sessionstart
session_start();
//login system
setcookie("id", "", time() - 30);
setcookie("pass", "", time() - 30);
header("Location:". "/");