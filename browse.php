<?php 

include ("includes/includedFiles.php");

?>

<h1 class="pageHeadingBig">You Might Also Like</h1>

<div class="gridViewContainer">

  <?php
    
    // Fetch album details
    $albumQuery = mysqli_query($con, "SELECT * FROM albums ORDER BY rand() LIMIT 10");
    
    while($row = mysqli_fetch_assoc($albumQuery)) {      
      echo "<div class='gridViewItem'>
              <span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
                <img src='" . $row['artworkPath'] . "'>              
                
                <div class='gridViewInfo'>"
                  . $row['title']. 
                "</div>
              </span>                      
            </div>";
          }
    ?>

</div>
          
