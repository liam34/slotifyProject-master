<?php include 'includes/header.php';

if(isset($_GET['id'])) {
    $albumId = $_GET['id'];
}
else {
    header("Location: index.php");
}

$album = new Album($con, $albumId);
/* 
 * Calling Artist object in Line 15 below will be futile, because to get the correct Artist, we first have to use the 
 * album ID received from Line 4 above to derive the relevant Artist ID from the album table and then pass this
 * Artist ID to the method call in line 18 to get the exact/correct Artist Name. * 
 */
 // $artist = new Artist($con, $album['artist']); 
$artist = $album->getArtist();

?>

<div class="entityInfo">

    <div class="leftSection">
        <img src="<?php echo $album->getArtworkPath(); ?>">             
    </div>

    <div class="rightSection">
        <h2><?php echo $album->getTitle(); ?></h2>
        <p>By <?php echo $artist->getName(); ?></p>
        <p><?php echo $album->getNumberOfSongs(); ?> songs</p>   
    </div>

</div>

<div class="tracklistContainer">
    <ul class="tracklist">

        <?php
        $songIdArray = $album->getSongIds();

        $i = 1;
        foreach($songIdArray as $songId) {

            $albumSong = new Song($con, $songId);
            /* Here too the logic is same as expalined in Line 12 above, as to why we not using 
            * $album->getArtist() from Line 17 and instead using the object->method-call in Line 48 below            *
            */
            $albumArtist = $albumSong->getArtist();              
            
            echo "<li class='tracklistRow'>
                <div class='trackCount'>
                    <img class='play' src='assets/images/icons/play-white.png'>
                    <span class='trackNumber'>$i</span>
                </div>

                <div class='trackInfo'>
                    <span class='trackName'>{$albumSong->getTitle()}</span>
                    <span class='artistName'>{$albumArtist->getName()}</span>              
                </div>

                <div class='trackOptions'>
                    <img class='optionButton' src='assets/images/icons/more.png'>
                </div>

                <div class='trackDuration'>
                    <span class='duration'>{$albumSong->getDuration()}</span>
                </div>
           </li>";

           $i++;

        }

        ?>
    
    </div>

</div>
          
<?php include 'includes/footer.php'; ?>