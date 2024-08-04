<?php
// backend/db.php
class Database {
    private static $instance = null;
    private $conn;

    // Database configuration
    private $host = 'srv1299.hstgr.io';
    private $username = 'u498377835_filmy';
    private $password = 'Admin123321.';
    private $database = 'u498377835_filmy';

    // Private constructor to prevent multiple instances
    private function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Public static method to get the single instance
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the database connection
    public function getConnection() {
        return $this->conn;
    }

    // Close the database connection
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            self::$instance = null;
        }
    }

    // Login function to validate user credentials and return user ID
    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id FROM uzivatele WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user ? $user['id'] : false;
    }

    // Function to get profiles based on user ID
    public function getProfilesByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM profily WHERE id_uzivatele = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $profiles = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $profiles;
    }

    // Function to create a new profile
    public function createNewProfile($userId, $profileName) {
        $stmt = $this->conn->prepare("INSERT INTO profily (id_uzivatele, jmeno_profilu) VALUES (?, ?)");
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param('is', $userId, $profileName);
        $stmt->execute();
        if ($stmt->error) {
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        }
        $stmt->close();
    }

    // Function to set series watch time
    public function setSeriesWatchTime($profileId, $serialId, $serie, $epizoda, $cas, $celkovyCas) {
        $stmt = $this->conn->prepare("INSERT INTO serialy_watchtime (profile_id, serial_id, serie, epizoda, cas, celkovy_cas) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }
        $stmt->bind_param('iiisii', $profileId, $serialId, $serie, $epizoda, $cas, $celkovyCas);
        $stmt->execute();
        if ($stmt->error) {
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        }
        $stmt->close();
    }

    // Function to get series watch time
    public function getSeriesWatchTime($profileId, $serialId, $serie, $epizoda) {
        $stmt = $this->conn->prepare("SELECT cas FROM serialy_watchtime WHERE profile_id = ? AND serial_id = ? AND serie = ? AND epizoda = ? ORDER BY cas DESC LIMIT 1");
        $stmt->bind_param('iiis', $profileId, $serialId, $serie, $epizoda);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['cas'] : "0";
    }

    // Function to get all series
    public function getSerialy() {
        $stmt = $this->conn->prepare("SELECT * FROM serialy");
        $stmt->execute();
        $result = $stmt->get_result();
        $serialy = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $serialy;
    }

    // Function to get series by ID
    public function getSerialById($serialId) {
        $stmt = $this->conn->prepare("SELECT * FROM serialy WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $serialId);
        $stmt->execute();
        $result = $stmt->get_result();
        $serial = $result->fetch_assoc();
        $stmt->close();
        return $serial;
    }

    // Function to get watched series for a profile
    public function getRozdivaneSerialy($profileId) {
        $stmt = $this->conn->prepare("
            SELECT s.* 
            FROM serialy_watchtime swt
            JOIN serialy s ON swt.serial_id = s.id
            WHERE swt.profile_id = ?
            GROUP BY s.id
            LIMIT 8
        ");
        $stmt->bind_param("i", $profileId);
        $stmt->execute();
        $result = $stmt->get_result();
        $serialy = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $serialy;
    }

    // Function to get series progress for a profile
    public function getPostupSerialu($profileId, $serial) {
        $stmt = $this->conn->prepare("
            SELECT serie, epizoda, cas, celkovy_cas 
            FROM serialy_watchtime 
            WHERE profile_id = ? AND serial_id = ? 
            ORDER BY serie DESC, epizoda DESC, cas DESC 
            LIMIT 1
        ");
        $stmt->bind_param("ii", $profileId, $serial);
        $stmt->execute();
        $result = $stmt->get_result();
        $postup = $result->fetch_assoc();
        $stmt->close();
        return $postup;
    }

    // Function to get top 5 movies
    public function getTop5Filmy() {
        $stmt = $this->conn->prepare("SELECT * FROM filmy WHERE category = 'top5'");
        $stmt->execute();
        $result = $stmt->get_result();
        $filmy = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $filmy;
    }
}

