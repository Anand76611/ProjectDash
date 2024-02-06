<?php
include 'dconfig.php';

// Assuming you want to search for records related to a specific customer ID or customer name
if (isset($_POST['dateSearch'])) {
    $targetTransaction = $_POST['dateSearch'];

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT  ts.tname, ts.taskid, t.date, ts.image, ts.status FROM  transaction t
            INNER JOIN task ts ON t.tid = ts.tid
            WHERE t.date = ?";

    $stmt = $conn->prepare($sql);

    // Bind parameter
    $stmt->bind_param("s", $targetTransaction);

    // Execute the statement
    $stmt->execute();

    // Get result set
    $result = $stmt->get_result();

    $data = array();

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'tname' => $row['tname'],
                    'date' => $row['date'],
                    'status' => $row['status'],
                    'image' => $row['image'],
                    'taskid' => $row['taskid']
                );
            }
        }
        $result->close();
    }

    // Output JSON data
    header('Content-Type: application/json');
    echo json_encode($data);

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(array('error' => 'Date not provided'));
}

// Close the connection
$conn->close();
?>
