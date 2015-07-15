<?php

session_start();

    $redirect = 'index.php';
    session_destroy();

header('Location:' . $redirect);
