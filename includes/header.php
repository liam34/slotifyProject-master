<?php

include 'includes/config.php';
include 'includes/classes/Artist.php';
include 'includes/classes/Album.php';
include 'includes/classes/Song.php';

if (isset($_SESSION['userLoggedIn'])) {
  $userLoggedIn = $_SESSION['userLoggedIn'];
  echo "<script>userLoggedIn = '$userLoggedIn';</script>";
}
else {
  header("Location: register.php");
}
?>
<html>
<head>
  <title>Welcome to My Music Player!</title>

  <link rel="stylesheet" type="text/css" href="assets/css/style.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <script src="assets/js/script.js"></script>
</head>

<body>

  <div id="mainContainer">

    <div id="topContainer">

      <?php include 'includes/navBarContainer.php'; ?>

        <div id="mainViewContainer">

            <div id="mainContent">