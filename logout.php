<?php

require_once __DIR__ . '/php/Auth.php';

clearAuthSession();
header('Location: login.php');
exit;
