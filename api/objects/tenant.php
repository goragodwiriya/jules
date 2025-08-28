<?php
class Tenant {
    // database connection and table name
    private $conn;
    private $table_name = "tenants";

    // object properties
    public $id;
    public $name;
    public $phone;
    public $email;
    public $id_card;
    public $address;
    public $created_at;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // read tenants
    function read() {
        $query = "SELECT id, name, phone, email FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // create tenant
    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, phone=:phone, email=:email, id_card=:id_card, address=:address";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id_card = htmlspecialchars(strip_tags($this->id_card));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id_card", $this->id_card);
        $stmt->bindParam(":address", $this->address);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read one tenant
    function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->name = $row['name'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->id_card = $row['id_card'];
            $this->address = $row['address'];
            return true;
        }
        return false;
    }

    // Update tenant
    function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, phone = :phone, email = :email, id_card = :id_card, address = :address WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id_card = htmlspecialchars(strip_tags($this->id_card));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id_card', $this->id_card);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete tenant
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // read unassigned tenants
    function readUnassigned() {
        $query = "SELECT
                    t.id, t.name
                FROM
                    " . $this->table_name . " t
                LEFT JOIN
                    contracts c ON t.id = c.tenant_id AND c.is_active = 1
                WHERE
                    c.id IS NULL
                ORDER BY
                    t.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
