<?php
session_start();

include 'dconfig.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = $_POST['cid'];
    $name = $_POST['name'];

    // Insert data into customer table
    $sqlCustomer = "INSERT INTO customer (custid, name) VALUES ('$cid', '$name')";

    try {
        $resultCustomer = mysqli_query($conn, $sqlCustomer);

        // Check if customer data is inserted successfully
        if ($resultCustomer) {
            echo "<script>alert('Added successfully!'); window.location.href = 'dashboard.html';</script>";
            exit(); // Ensure that the script stops executing after the redirect
        } else {
            echo "<script>alert('Oops! Something went wrong while inserting into the customer table.')</script>";
        }
    } catch (mysqli_sql_exception $e) {
        // Check for duplicate entry error
        if ($e->getCode() == 1062) {
            echo "<script>alert('Customer with ID $cid already exists.'); window.location.href = 'customer.html';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}
?>
