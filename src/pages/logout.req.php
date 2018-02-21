<?php
setcookie("mangadex", "", $timestamp - 3600, "/");

$new_token = md5($token . $timestamp);

$db->query(" UPDATE mangadex_users SET token = '$new_token' WHERE user_id = $user->user_id LIMIT 1; ");

session_unset(); //remove all session variables
session_destroy(); //destroy the session

print display_alert("success", "Success", "You have logged out."); 

$result = 1;

$db->query("INSERT INTO mangadex_logs_actions (action_id, action_name, action_user_id, action_timestamp, action_ip, action_result, action_details) 
VALUES (NULL, 'logout', $user->user_id, $timestamp, '$ip', $result, ''); ");

header('Location: /');

exit;

?>