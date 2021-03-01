<?php
// Script to destroy a session
session_start();
session_unset();
session_destroy();
header('Location: /');
?>