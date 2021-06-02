<?php

include '../../config.php';

// songId is got from 'nowPlayingBar.php'-Line 13, 26
if(isset($_POST['songId'])) {
    $songId = $_POST['songId'];

    $query = mysqli_query($con, "SELECT * FROM songs WHERE id='$songId'");

    $resultArray = mysqli_fetch_assoc($query);   

    // Converting and echoing PHP array ($resultArray) in JSON
    echo json_encode($resultArray);
}

?>