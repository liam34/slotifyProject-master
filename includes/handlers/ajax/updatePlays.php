<?php

include '../../config.php';

// songId is got from 'nowPlayingBar.php'-Line 13, 26
if(isset($_POST['songId'])) {
    $songId = $_POST['songId'];

    // Update the song play count
    $query = mysqli_query($con, "UPDATE songs SET plays=plays+1 WHERE id='$songId'");   
}

?>