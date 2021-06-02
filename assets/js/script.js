/*
* Audio player controller script
*/

// currentPlaylist array value is got from nowPlayingBar.php-Line-22
var currentPlaylist = Array();
// Audio class is instantiated by audioElement-object at nowPlayingBar.php-Line-24
var audioElement;
var mouseDown = false;
// Sets the currentIndex of playlist to 0
var currentIndex = 0;
// Initially repeat is set to false else the song will go on repeating when next song button is pressed
var repeat = false;

function formatTime(seconds) {
    var time = Math.round(seconds);
    var minutes = Math.floor(time/60);
    var seconds = time - (minutes * 60);   
    
    // check if seconds is less than zero and prefix it with 0 if any
    // eg: time of 3.4 will be outputted as 3:04
    var extraZero = (seconds < 10) ? "0" : '';

    return minutes + ":" + extraZero + seconds;
}

// Progress bar update - current time  & remaining time
function updateTimeProgressBar(audio) {
    // Current real time
    $(".progressTime.current").text(formatTime(audio.currentTime));
    // Remaining real time
    $(".progressTime.remaining").text(formatTime(audio.duration-audio.currentTime));

    // Progress bar real time increment - this is calcualted & displayed in percentage
    // duration is the total length of the song
    // currentTtime is the current position of the song
    var progress = audio.currentTime / audio.duration * 100;
    // progress is the css class of the progress bar
    $(".playbackBar .progress").css("width", progress + "%");
}

// To display the volume progress bar to the volume set by the user
function updateVolumeProgressBar(audio) {
    // Volume val is a decimal value from 0-1. We multiply it by 100 to get the volume percentage    
    var volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

// Audio Object Constructor which is akin to a class in JS
function Audio() {

    this.currentlyPlaying;

    // Read more on Audio Element here: https://tinyurl.com/y76sl54x
    // all the 'this' declaration below belong to the audio object in line 28   
    this.audio = document.createElement('audio');

    // Play or Repeat Next Song when Current Song ends
    this.audio.addEventListener("ended", function() {
        nextSong();
    });

    // canplay means when the audio player is finally ready/able to play a song
    // This is only possible when there are no errors 
    // More on canplay event: https://tinyurl.com/y9gyposs    
    this.audio.addEventListener("canplay", function() {
        // duration is the total duration of a song        
        var duration = formatTime(this.duration);
        // Progress bar remaining time update
        $(".progressTime.remaining").text(duration);        
    });

    // Progress bar current time update
    this.audio.addEventListener("timeupdate", function() {
        if(this.duration) {
            // 'this' is the audio object in line 28
            updateTimeProgressBar(this);
        }
    });

    this.audio.addEventListener("volumechange", function() {
        // this refers to the audio element
        updateVolumeProgressBar(this);
    })

    // track is the Object containing all the songs data. See nowPlayingBar.php-Line-41
    // Property of Object track is obtained by parsing the JSON data
    this.setTrack = function(track) {
    // Assigning object track to property. See nowPlayingBar.php-Line-41
        this.currentlyPlaying = track;
        this.audio.src = track.path; // track.path points to 'column path' in 'DB table songs.path'
    }

    this.play = function() {
        this.audio.play();
    }

    this.pause = function() {
        this.audio.pause();
    }

    this.setTime = function(seconds) {
        this.audio.currentTime = seconds
    }    
}
