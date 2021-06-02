<?php

// Fetch songs to generate a random playlist
$songQuery = mysqli_query($con, 'SELECT id FROM songs ORDER BY RAND() LIMIT 10');

$resultArray = array();
    
while($row = mysqli_fetch_assoc($songQuery)) {
  //Pushing the song Ids into $resultArray
  array_push($resultArray, $row['id']);
}

// Converting PHP array ($resultArray) into JSON
$jsonArray = json_encode($resultArray);
?>

<script>
// To Check contents of $jsonArray object
// console.log(<?php //echo $jsonArray; ?> );

// document.ready means when the page first loads
// Read more at: https://tinyurl.com/ybywrnra
$(document).ready(function() {
  currentPlaylist = <?php echo $jsonArray; ?>;
  // audioElement object
  audioElement = new Audio();
  // currentPlaylist[0] set from Line 22
  setTrack(currentPlaylist[0], currentPlaylist, false);
  // Displays the volume to full depicting the max volume when the page is loaded
  // Note that the function below has nothing to do with the volume being set to full
  updateVolumeProgressBar(audioElement.audio);

  $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function(e) {
    // Prevents default behavious of an event
    e.preventDefault();
  })
  
  // Progress bar controller
  $(".playbackBar .progressBar").mousedown(function() {
    mouseDown = true;
  });

  $(".playbackBar .progressBar").mousemove(function(e) {
    if(mouseDown) {
      //Note the song's current time depending upon the position of mouse cursor on the progress bar
      // this refers to the progress bar viz. 'playbackBar .progressBar'
      timeFromOffset(e, this);
    }
  });

  // Notes the current time even when the mouse cursor is up, but stil hovered over the progressBar region
  $(".playbackBar .progressBar").mouseup(function(e) {
    timeFromOffset(e,this);   
  });

  // Set mouseDown to false if the mouse has moved away from the progress bar region
  $(document).mouseup(function() {
    mouseDown = false;
  });

  // Volume bar controller
  $(".volumeBar .progressBar").mousedown(function() {
    mouseDown = true;
  });

  $(".volumeBar .progressBar").mousemove(function(e) {
    if(mouseDown) {

      // this refers to .volumeBar .progressBar
      var percentage =  e.offsetX / $(this).width();

      if(percentage >=0 && percentage <=1) {        
        audioElement.audio.volume = percentage;
      }      
    }
  });

  // Notes the current time even when mouse is up but still hovered over the volume bar region
  $(".volumeBar .progressBar").mouseup(function(e) {
    // this refers to the class '.volumeBar .progressBar'
    var percentage =  e.offsetX / $(this).width();

    if(percentage >=0 && percentage <=1) {        
        audioElement.audio.volume = percentage;
      }
  });
});

// Offset means how far the current progress bar position is or has moved from its initial position
function timeFromOffset(mouse, progressBar) {
  var percentage = mouse.offsetX / $(progressBar).width() * 100;
  var seconds = audioElement.audio.duration * (percentage / 100);
  audioElement.setTime(seconds);
}

// Play Previous song
function prevSong() {
  // If currentTime of 'current song' is more than 3 seconds then song's currentTime will be set to 0
  // This is how all audio player functions
  // if(audioElement.audio.currentTime >= 3 || currentIndex == 0 ) {
  if(audioElement.audio.currentTime >= 3) {
    audioElement.setTime(0);
  }
  // trackToPlay is the trackId, currentPlaylist is the new playlist
  // true means whether to play the song automatically or not
  else if(currentIndex == 0 && audioElement.audio.currentTime < 3) {
    currentIndex = currentPlaylist.length - 1;
    // console.log('INDEX = ' + currentIndex);
    setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
  }
  else {
    // If currentTime is less than 3 seconds then play Previous song
    currentIndex = currentIndex - 1;
    setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
  }
}


// Paly Next song or Repeat song
function nextSong() {

  // Repeat Song
  // 'repeat' is set from the 'class repeat' at Line 254 below
  if(repeat) {
    // If repeat is true then set the song time to 0 and then play the song
    audioElement.setTime(0);
    playSong();
    return;
  }

  // Next Song
  // Checks if currentIndex is equal to max index value of the playlist; if yes then set currentIndex value to 0.
  // What this does is if currentIndex reaches max value of playlist, say 9, meaning there are 9 songs in the album
  // now when the user presses the Next button; since now the index is set to 0, the first song will be played 
  if(currentIndex == currentPlaylist.length-1) {
    currentIndex = 0;
  } else {
    currentIndex++;
  }
  // This is saying use the currentPlaylist to get the item at this currentIndex
  var trackToPlay = currentPlaylist[currentIndex];
  // trackToPlay is the trackId, currentPlaylist is the new playlist
  // true means whether to play the song automatically or not
  setTrack(trackToPlay, currentPlaylist, true);
}

// Set the Repeat icon
function setRepeat() {
  // This logic is mainly used to set the default repeat icon when the song page loads initially
  // The default repeat icon is 'repeat.png'
  repeat = !repeat;
  var imageName = repeat ? "repeat-active.png" : "repeat.png";
  // controlButton.repeat is the class name at Line 254 
  $(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
}

// trackID = currentPlaylist[0] from Line 28, which is the value of the first Song Id fetched from the DB
function setTrack(trackId, newPlaylist, play) {    
  // Sets the current song index of the playlist
  currentIndex = currentPlaylist.indexOf(trackId);
  // When the song is changed, pause the song. You won't notice the song pausing, but it's good to have this feature
  pauseSong();

  // AJAX call to get song data from DB. songId is got from jsonArray[0] in Line 14, 28
  // jsonArray[0] is the value of the first Song ID fetched from the DB
  // songId: trackId is the Ajax input
  // function(data) is the output returned by the Ajax call, which is in the form of JSON 
  $.post("includes/handlers/ajax/getSongJson.php", { songId: trackId }, function(data) {   

    // Converting JSON data into JS Object called track, so that JS can read it
    // If JSON data isn't parsed into JSON JS won't be able to read it, resulting in an error
    var track = JSON.parse(data);
    // console.log(track);

    // Creating jQuery Object to output Track Title in Line 89. title is the column name in the songs db
    $(".trackName span").text(track.title);

    // AJAX call to get Artist data from DB
    // track.artist is got from the Ajax call in Line 36
    // function(data) is the output returned by the Ajax call, which is the JSON data
    $.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist }, function(data) {
      var artist = JSON.parse(data);
      //console.log(artist.name);
      // Creating jQuery Object to output Track Title in Line 93
      $(".artistName span").text(artist.name);
    });

     // AJAX call to get Album data from DB
     // track.album is got from the Ajax call in Line 36
    // function(data) is the output returned by the Ajax call, which is the JSON data 
    $.post("includes/handlers/ajax/getAlbumJson.php", { albumId: track.album }, function(data) {
      var album = JSON.parse(data);
      //console.log(album.title);
      //console.log(album.genre);
      $(".albumLink img").attr("src", album.artworkPath);      
    }); 

    // track.path is got from the Ajax call in Line 36
    // path refers to the column name in the songs table
    audioElement.setTrack(track);
    playSong();
  });

  if(play) {
    audioElement.play();
  }
}

function playSong() {
  if(audioElement.audio.currentTime === 0) {
    // console.log(audioElement);
    // AJAX call to UPDATE song play count in the songs table
    $.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id });   
  }
  
  $(".controlButton.play").hide();
  $(".controlButton.pause").show();
  audioElement.play();
}

function pauseSong() {
  $(".controlButton.play").show();
  $(".controlButton.pause").hide();
  audioElement.pause();
}
</script>
      
<div id="nowPlayingBarContainer">

  <div id="nowPlayingBar">

    <div id="nowPlayingLeft">
      <div class="content">
        <span class="albumLink">
          <img class="albumArtwork" src="" alt="Album cover">
        </span>

        <div class="trackInfo">

          <span class="trackName">
            <span></span>
          </span>

          <span class="artistName">
            <span></span>
          </span>

        </div>
      </div>
    </div>

    <div id="nowPlayingCenter">

      <div class="content playerControls">

        <div class="buttons">
          <button class="controlButton shuffle" title="Shuffle">
            <img src="assets/images/icons/shuffle.png" alt="Shuffle">
          </button>

          <button class="controlButton previous" title="Previous song" onclick="prevSong()">
            <img src="assets/images/icons/previous.png" alt="Previous">
          </button>

          <button class="controlButton pause" title="Pause song" style="display:none;">
            <img src="assets/images/icons/pause.png" alt="Pause" onclick="pauseSong()">
          </button>

          <button class="controlButton play" title="Play song" onclick="playSong()">
            <img src="assets/images/icons/play.png" alt="Play">
          </button>

          <button class="controlButton next" title="Next song" onclick="nextSong()">
            <img src="assets/images/icons/next.png" alt="Next">
          </button>

          <button class="controlButton repeat" title="Repeat song" onClick="setRepeat()">
            <img src="assets/images/icons/repeat.png" alt="Repeat song">
          </button>

        </div>

        <div class="playbackBar">

          <span class="progressTime current">0.00</span>

          <div class="progressBar">
            <div class="progressBarBg">
              <div class="progress"></div>
            </div>            
          </div>

          <span class="progressTime remaining">0.00</span>
        </div>
      </div>        
    </div>

      <div id="nowPlayingRight">

        <div class="volumeBar">

          <button class="controlButton volume" title="Volume button">
            <img src="assets/images/icons/volume.png" alt="Volume">
          </button>
          
          <div class="progressBar">
            <div class="progressBarBg">
              <div class="progress"></div>
            </div>
          </div> 
                   
        </div>              
      </div>
  </div>
</div>
