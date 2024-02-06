<?php
include 'dconfig.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $custId = $_POST["custId"];

    // Fetch customer details from the database based on CustID
    $stmt = $conn->prepare("SELECT  FROM customer WHERE custid = ?");
    $stmt->bind_param("i", $custId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(array('success' => true, 'customerName' => $row['name']));
    } else {
        echo json_encode(array('success' => false));
    }
}
?>
