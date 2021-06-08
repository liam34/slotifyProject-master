<?php

class Artist {

    private $con;
    private $id;    

    public function __construct($con, $id) {
        $this->con = $con;
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
    
    // Return artist name
    public function getName() {
        // Fetch artist details
        $artistQuery = mysqli_query($this->con, "SELECT name FROM artists WHERE id='$this->id'");
        $artist = mysqli_fetch_assoc($artistQuery);
        return $artist['name'];
    }

    public function getSongIds() {
        // Fetch song details
        $query = mysqli_query($this->con, "SELECT id FROM songs WHERE artist ='$this->id' ORDER BY plays ASC");
       
        $array = array();

        while($row = mysqli_fetch_array($query)) { //change back to assoc
             array_push($array, $row['id']);
        }

        return $array;
    }
}
  
?>
