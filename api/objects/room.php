<?php
class Room {
    // database connection and table name
    private $conn;
    private $table_name = "rooms";

    // object properties
    public $id;
    public $room_number;
    public $room_type;
    public $price;
    public $status;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // read rooms
    function read() {
        // select all query
        $query = "SELECT id, room_number, room_type, price, status FROM " . $this->table_name . " ORDER BY room_number ASC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // used when filling up the update form
    function readOne() {
        // query to read single record
        $query = "SELECT room_number, room_type, price, status FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of room to be updated
        $stmt->bindParam(1, $this->id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->room_number = $row['room_number'];
        $this->room_type = $row['room_type'];
        $this->price = $row['price'];
        $this->status = $row['status'];
    }

    // create room
    function create() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET room_number=:room_number, room_type=:room_type, price=:price, status=:status";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->room_number = htmlspecialchars(strip_tags($this->room_number));
        $this->room_type = htmlspecialchars(strip_tags($this->room_type));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // bind values
        $stmt->bindParam(":room_number", $this->room_number);
        $stmt->bindParam(":room_type", $this->room_type);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":status", $this->status);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // update the room
    function update() {
        // update query
        $query = "UPDATE " . $this->table_name . "
                SET
                    room_number = :room_number,
                    room_type = :room_type,
                    price = :price,
                    status = :status
                WHERE
                    id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->room_number = htmlspecialchars(strip_tags($this->room_number));
        $this->room_type = htmlspecialchars(strip_tags($this->room_type));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind new values
        $stmt->bindParam(':room_number', $this->room_number);
        $stmt->bindParam(':room_type', $this->room_type);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // delete the room
    function delete() {
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind id of record to delete
        $stmt->bindParam(1, $this->id);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // read available rooms
    function readAvailable() {
        $query = "SELECT id, room_number, price FROM " . $this->table_name . " WHERE status = 'available' ORDER BY room_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
