<?php
class Contract {
    private $conn;
    private $table_name = "contracts";

    public $id;
    public $room_id;
    public $tenant_id;
    public $start_date;
    public $end_date;
    public $deposit;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    function read() {
        $query = "SELECT
                    c.id, c.start_date, c.is_active,
                    r.room_number,
                    t.name as tenant_name
                FROM
                    " . $this->table_name . " c
                    LEFT JOIN
                        rooms r ON c.room_id = r.id
                    LEFT JOIN
                        tenants t ON c.tenant_id = t.id
                ORDER BY
                    c.start_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function create() {
        // --- Transaction Start ---
        $this->conn->beginTransaction();

        try {
            // 1. Create the contract
            $query = "INSERT INTO " . $this->table_name . " SET room_id=:room_id, tenant_id=:tenant_id, start_date=:start_date, deposit=:deposit, is_active=1";
            $stmt = $this->conn->prepare($query);

            $this->room_id = htmlspecialchars(strip_tags($this->room_id));
            $this->tenant_id = htmlspecialchars(strip_tags($this->tenant_id));
            $this->start_date = htmlspecialchars(strip_tags($this->start_date));
            $this->deposit = htmlspecialchars(strip_tags($this->deposit));

            $stmt->bindParam(":room_id", $this->room_id);
            $stmt->bindParam(":tenant_id", $this->tenant_id);
            $stmt->bindParam(":start_date", $this->start_date);
            $stmt->bindParam(":deposit", $this->deposit);

            if (!$stmt->execute()) {
                throw new Exception("Unable to create contract.");
            }

            // 2. Update the room status to 'occupied'
            $room_query = "UPDATE rooms SET status = 'occupied' WHERE id = :room_id";
            $room_stmt = $this->conn->prepare($room_query);
            $room_stmt->bindParam(":room_id", $this->room_id);

            if (!$room_stmt->execute()) {
                throw new Exception("Unable to update room status.");
            }

            // --- Commit Transaction ---
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // --- Rollback Transaction ---
            $this->conn->rollBack();
            // You can log the error message: $e->getMessage();
            return false;
        }
    }
}
?>
