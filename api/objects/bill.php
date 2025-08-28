<?php
class Bill {
    private $conn;
    private $table_name = "bills";

    public $id;
    public $contract_id;
    public $bill_date;
    public $due_date;
    public $total_amount;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    function read() {
        $query = "SELECT
                    b.id, b.bill_date, b.due_date, b.total_amount, b.status,
                    r.room_number,
                    t.name as tenant_name
                FROM
                    " . $this->table_name . " b
                    LEFT JOIN contracts c ON b.contract_id = c.id
                    LEFT JOIN rooms r ON c.room_id = r.id
                    LEFT JOIN tenants t ON c.tenant_id = t.id
                ORDER BY
                    b.bill_date DESC, r.room_number ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function generate() {
        // 1. Get all active contracts
        $contract_query = "SELECT id, room_id FROM contracts WHERE is_active = 1";
        $contract_stmt = $this->conn->prepare($contract_query);
        $contract_stmt->execute();

        $contracts = $contract_stmt->fetchAll(PDO::FETCH_ASSOC);
        $bills_created_count = 0;

        foreach($contracts as $contract) {
            $contract_id = $contract['id'];
            $room_id = $contract['room_id'];

            // 2. Check if a bill for this month already exists for this contract
            $check_query = "SELECT id FROM " . $this->table_name . " WHERE contract_id = ? AND YEAR(bill_date) = YEAR(CURDATE()) AND MONTH(bill_date) = MONTH(CURDATE())";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(1, $contract_id);
            $check_stmt->execute();

            if($check_stmt->rowCount() == 0) {
                // 3. No bill exists, so create one
                // First, get the room price
                $price_query = "SELECT price FROM rooms WHERE id = ?";
                $price_stmt = $this->conn->prepare($price_query);
                $price_stmt->bindParam(1, $room_id);
                $price_stmt->execute();
                $room = $price_stmt->fetch(PDO::FETCH_ASSOC);
                $room_price = $room['price'];

                // 4. Insert the new bill
                $insert_query = "INSERT INTO " . $this->table_name . " SET contract_id=:contract_id, bill_date=CURDATE(), due_date=LAST_DAY(CURDATE()), room_rent=:room_rent, total_amount=:total_amount, status='unpaid'";
                $insert_stmt = $this->conn->prepare($insert_query);

                $insert_stmt->bindParam(":contract_id", $contract_id);
                $insert_stmt->bindParam(":room_rent", $room_price);
                $insert_stmt->bindParam(":total_amount", $room_price);

                if($insert_stmt->execute()){
                    $bills_created_count++;
                }
            }
        }
        return $bills_created_count;
    }

    function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
