<?php
require_once 'includes/functions.php';
session_destroy();
header('Location: index.php?msg=You+have+been+logged+out+successfully');
exit();
