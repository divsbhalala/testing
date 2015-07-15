<?php
session_start();
/* --------check for access  token is exists -------------- */
if(empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: clearsession.php');
}


$oauth_token=$_SESSION['access_token']['oauth_token'];
$oauth_token_secret=$_SESSION['access_token']['oauth_token_secret'];
//print_r($_SESSION);exit;

require_once 'tweetconnection.php';
$conn = new tweetconnection();
$request = json_encode($_POST);
$request = json_decode($request);
 //$userlist=$conn->searchUser($oauth_token,$oauth_token_secret);
 //echo '<pre>';
  // print_r($userlist);

if(isset($request->username) && !empty($request->username))
{
    $userlist=$conn->searchUser($request->username,$oauth_token,$oauth_token_secret);
    //echo '<pre>';
    //print_r($userlist);
   // echo '</pre>';
    echo json_encode($userlist);
}