<?php

// Include your database connection logic from dconfig.php
include 'dconfig.php';
 
// Check if the taskid is set in the POST request
if (isset($_POST['taskid'])) {
    // Sanitize the input to prevent SQL injection
    $taskid = mysqli_real_escape_string($conn, $_POST['taskid']);
    // Perform a query to get the image path based on taskid
    $sql = "SELECT image FROM task WHERE taskid = $taskid";

    $result = $conn->query($sql);

    if ($result) {
        // Check if any rows were returned
        if ($result->num_rows > 0) {
            // Fetch the result as an associative array
            $row = $result->fetch_assoc();

            // Get the image path from the array
            $imagePath = $row['image'];

            // Send the image path back to the client
            echo json_encode(['imagePath' => $imagePath]);
        } else {
            // If no result found, send an empty response or an error message
            echo json_encode(['error' => 'Image path not found for taskid: ' . $taskid]);
        }
    } else {
        // If there was an error with the query, send an error message
        echo json_encode(['error' => 'Query error: ' . $conn->error]);
    }
} else {
    // If taskid is not set in the POST request, send an error message
    echo json_encode(['error' => 'Taskid not provided in the request']);
}

// Close the database connection
$conn->close();

?>