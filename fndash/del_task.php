

<?php
include 'dconfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use hardcoded values for testing (replace with actual data in a real-world scenario)
    $tid = 1;
    $taskid = 1;

    // Assuming your database connection is established in dconfig.php
    $conn = getDBConnection(); // Assuming getDBConnection() is a function in dconfig.php that returns a mysqli connection

    // Begin a transaction to ensure atomicity
    $conn->begin_transaction();

    // Step 1: Delete the corresponding transaction
    $stmtTransaction = $conn->prepare('DELETE FROM transaction WHERE tid = ?');
    $stmtTransaction->bind_param('i', $tid);

    if ($stmtTransaction->execute()) {
        // Step 2: Delete the task
        $stmtTask = $conn->prepare('DELETE FROM task WHERE taskid = ?');
        $stmtTask->bind_param('i', $taskid);

        if ($stmtTask->execute()) {
            // Both deletion steps successful, commit the transaction
            $conn->commit();
            echo 'success';
        } else {
            // Failed to delete the task, rollback the transaction
            $conn->rollback();
            echo 'error';
        }

        // Close the statement for task
        $stmtTask->close();
    } else {
        // Failed to delete the transaction, rollback the transaction
        $conn->rollback();
        echo 'error';
    }

    // Close the statement for transaction and the connection
    $stmtTransaction->close();
    $conn->close();
} else {
    // Invalid request method
    echo 'error';
}
?>









