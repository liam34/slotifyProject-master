<?php

include '../../config.php';

if(isset($_POST['songId'])) {
    $songId = $_POST['songId'];

    // Update the song play count
    $query = mysqli_query($con, "UPDATE songs SET plays=plays+1 WHERE id='$songId'");   
}

?>