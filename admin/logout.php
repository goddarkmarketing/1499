<?php
require_once __DIR__ . '/includes/auth.php';
start_session(app_config('session.admin_key'));
session_destroy();
header('Location: login.php');
