
<?php

require_once __DIR__ . "/condb.php";

$action = $_GET["action"] ?? "";

try {

    // แสดงข้อมูลทั้งหมด
    if ($action == "list") {

        $stmt = $conn->query(
            "SELECT * FROM categorys ORDER BY category_id DESC"
        );

        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ], JSON_UNESCAPED_UNICODE);
    }

    // เพิ่มประเภทสินค้า
    elseif ($action == "add") {

        $data = json_decode(file_get_contents("php://input"), true);

        $name = trim($data["category_name"] ?? "");

        if ($name == "") {
            throw new Exception("กรุณากรอกชื่อประเภทสินค้า");
        }

        $stmt = $conn->prepare(
            "INSERT INTO categorys (category_name) VALUES (?)"
        );

        $stmt->execute([$name]);

        echo json_encode([
            "success" => true,
            "message" => "เพิ่มข้อมูลสำเร็จ"
        ], JSON_UNESCAPED_UNICODE);
    }

    // แก้ไขประเภทสินค้า
    elseif ($action == "update") {

        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data["category_id"] ?? 0;
        $name = trim($data["category_name"] ?? "");

        if (!$id || $name == "") {
            throw new Exception("กรุณากรอกข้อมูลให้ครบ");
        }

        $stmt = $conn->prepare(
            "UPDATE categorys
             SET category_name = ?
             WHERE category_id = ?"
        );

        $stmt->execute([$name, $id]);

        echo json_encode([
            "success" => true,
            "message" => "แก้ไขข้อมูลสำเร็จ"
        ], JSON_UNESCAPED_UNICODE);
    }

    // ลบประเภทสินค้า
    elseif ($action == "delete") {

        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data["category_id"] ?? 0;

        if (!$id) {
            throw new Exception("ไม่พบรหัสประเภทสินค้า");
        }

        $stmt = $conn->prepare(
            "DELETE FROM categorys WHERE category_id = ?"
        );

        $stmt->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "ลบข้อมูลสำเร็จ"
        ], JSON_UNESCAPED_UNICODE);
    }

    else {
        throw new Exception("ไม่พบคำสั่งที่ต้องการ");
    }

} catch (Throwable $e) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

?>
