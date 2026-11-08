<?php
require 'config.php';
header('Location: ' . (isset($_SESSION['user_id']) ? 'welcome.php' : 'login.php'));
exit;
