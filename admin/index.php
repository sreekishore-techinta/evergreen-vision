<?php
require_once __DIR__ . '/../backend/config/helpers.php';
start_session();
if (is_logged_in()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
