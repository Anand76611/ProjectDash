<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Include your database connection file
include 'dconfig.php'; // Replace with the actual path to your database connection script

// Check if the dateSearch parameter is set in the POST request
if (isset($_POST['dateSearch'])) {
    // Sanitize and validate the date
    $dateSearch = $_POST['dateSearch'];
    $dateSearch = date('Y-m-d', strtotime($dateSearch));

    // Prepare and execute the query
    $stmt = $mysqli->prepare("SELECT * FROM transaction WHERE DATE(date) = ?");
    $stmt->bind_param("s", $dateSearch);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch data and encode it as JSON
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);

    // Close the statement and connection
    $stmt->close();
    $mysqli->close();
} else {
    // If dateSearch is not set, return an error response
    echo json_encode(array('error' => 'Date parameter not provided.'));
}
?>
