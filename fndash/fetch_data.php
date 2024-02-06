<?php
include 'dconfig.php';

$sql = "SELECT  ts.tname, t.date, ts.status, ts.image FROM transaction t
        INNER JOIN task ts ON t.tid = ts.tid";

$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
           
            'tname' => $row['tname'],
            'date' => $row['date'],
            'status' => $row['status'],
            'image' => $row['image']
        );
    }
}

// Close the connection
$conn->close();

// Output JSON data
header('Content-Type: application/json');
echo json_encode($data);
?>
