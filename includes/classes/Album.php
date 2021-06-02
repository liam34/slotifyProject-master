<?php

class Album {

    private $con;
    private $id;
    private $title;
    private $artistId;
    private $genre;
    private $artworkPath;

    public function __construct($con, $id) {
        $this->con = $con;
        $this->id = $id;
        
        // Fetch album details
        $query = mysqli_query($this->con, "SELECT * FROM albums WHERE id='$this->id'");
        $album = mysqli_fetch_array($query);

        $this->title = $album['title'];
        $this->artistId= $album['artist'];
        $this->genre = $album['genre'];
        $this->artworkPath = $album['artworkPath'];

    }
    
    // To access private class variables, you have to use public function
    // Read more at: https://tinyurl.com/y7pwh3tz
    public function getTitle() {
        return $this->title;        
    }

    /*
    * To get the Artist Name we have to make use of the Artist Class because
    * the album table just has the artist id. The artist table is storing the Artist Name
    */
    public function getArtist() {
        return new Artist($this->con, $this->artistId);
    }

    public function getGenre() {
        return $this->genre;        
    }

    public function getArtWorkPath() {
        return $this->artworkPath;
    }

    public function getNumberOfSongs() {
        /* By Reece: 
            $query = mysqli_query($this->con, "SELECT id FROM songs WHERE album='$this->id'");
            return mysqli_num_rows($query);*/
        /* Else you can try the solution below also
            $query = mysqli_query($this->con, "SELECT COUNT(*) FROM songs WHERE album='$this->id'");
            return $result['COUNT(*)'];
        */

        // Count total no. of songs in an album
        $query = mysqli_query($this->con, "SELECT COUNT(*) AS total FROM songs WHERE album='$this->id'");
        $result = mysqli_fetch_assoc($query);
        return $result['total'];      
    }

    public function getSongIds() {
        // Fetch song details
        $query = mysqli_query($this->con, "SELECT id FROM songs WHERE album ='$this->id' ORDER BY albumOrder ASC");
       
        $array = array();

        while($row = mysqli_fetch_assoc($query)) {
             array_push($array, $row['id']);
        }

        return $array;
    }
}
  
?>
