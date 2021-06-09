/* Audio player controller script*/
var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;

function openPage(url) {

    if(timer != null) {
        clearTimeout(timer);
    }

    if(url.indexOf("?") == -1) {
        url = url + "?";
    }
    var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
    console.log(encodedUrl);
    $("#mainContent").load(encodedUrl);
    $("body").scrollTop(0);
    history.pushState(null, null, url);
}

function creatPlaylist() {
    console.log(userLoggedIn);
    var popup = prompt("please enter the name of your playlist");

    if(popup != null) {
        
        $.post("includes/handlers/ajax/createPlaylist.php", { name: popup, username: userLoggedIn })
        .done(function(error){

            if(error != null) {
                alert(error);
                return;
            }
            //do something when ajax returns
            openPage("yourMusic.php");
        })

    }
}

function formatTime(seconds) {
    var time = Math.round(seconds);
    var minutes = Math.floor(time/60);
    var seconds = time - (minutes * 60);   
    
    var extraZero = (seconds < 10) ? "0" : '';

    return minutes + ":" + extraZero + seconds;
}

// Progress bar update - current time  & remaining time
function updateTimeProgressBar(audio) {
    // Current real time
    $(".progressTime.current").text(formatTime(audio.currentTime));
    // Remaining real time
    $(".progressTime.remaining").text(formatTime(audio.duration-audio.currentTime));
    var progress = audio.currentTime / audio.duration * 100;
    // progress is the css class of the progress bar
    $(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio) {   
    var volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

function playFirstSong() {
    setTrack(tempPlaylist[0], tempPlaylist, true);
}

function Audio() {

    this.currentlyPlaying;  
    this.audio = document.createElement('audio');

    
    this.audio.addEventListener("ended", function() {
        nextSong();
    });

     
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

  
    this.setTrack = function(track) {
    
        this.currentlyPlaying = track;
        this.audio.src = track.path; 
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
