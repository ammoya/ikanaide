<?php

require_once 'app/Activity.php';
require_once 'app/User.php';
$User = new User;
$Activity = new Activity;

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)  && $User -> validateSession()) {
    $like['user_id'] = $_COOKIE['user_id'];
    $like['post_id'] = intval($_GET['id']);
    if ($Activity -> like($like)) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);    
    } else {
        // Pasar por aqui significa que validateSession() en Activity::like() ha dado falso, por lo que provoco un logout.
        header('Location: /logout');
    }
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}