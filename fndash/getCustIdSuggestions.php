<?php
// Include the database configuration file
include_once 'dconfig.php';

// Fetch suggestions based on user input
$input = $_GET["input"];
$sql = "SELECT custid FROM customer WHERE CustID LIKE '%$input%' LIMIT 10";
$result = $conn->query($sql);

// Output suggestions in HTML format
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row["CustID"] . "'>";
    }
} else {
    echo "<option value='No suggestions'>";
}

// Close the database connection
$conn->close();
?>






