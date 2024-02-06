<?php
header('Content-Type: text/html; charset=utf-8');
include 'dconfig.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if 'data' is set in the POST request
    if (!isset($_POST["data"])) {
        echo "Error: 'data' key is not present in the POST request.";
        exit;
    }

    // Get the JSON data from the POST request
    $jsonData = $_POST["data"];

    // Decode the JSON data
    $decodedData = json_decode($jsonData, true);

    // Check for JSON decoding errors
    if ($decodedData === null) {
        echo "Error decoding JSON: " . json_last_error_msg();
        exit;
    }

    // Process the data as needed
    $existingCid = htmlspecialchars($decodedData["cid"]);
    $name = htmlspecialchars($decodedData["name"]);
    $date = htmlspecialchars($decodedData["date"]);

    // Check if the 'tasks' key is set and is an array
    if (isset($decodedData["tasks"]) && is_array($decodedData["tasks"])) {

        // Check if the 'custid' exists in the customer table
        $stmtCheckCust = $conn->prepare("SELECT custid FROM customer WHERE custid = ?");
        $stmtCheckCust->bind_param("i", $existingCid);
        $stmtCheckCust->execute();
        $resultCheckCust = $stmtCheckCust->get_result();
        $stmtCheckCust->close();

        if ($resultCheckCust->num_rows > 0) {
            // Customer exists, proceed with the transaction and task insertion

            // Update transaction information based on the date
            $stmtUpdateTransaction = $conn->prepare("UPDATE transaction SET date = ? WHERE custid = ? AND date = ?");
            $stmtUpdateTransaction->bind_param("sis", $date, $existingCid, $date);
            $resultUpdateTransaction = $stmtUpdateTransaction->execute();
            $stmtUpdateTransaction->close();

            if ($resultUpdateTransaction) {
                // Fetch the last updated transaction id
                $lastTransactionId = getLastUpdatedTransactionId($conn, $existingCid, $date);

                // Insert or update task information based on the transaction id
                foreach ($decodedData["tasks"] as $task) {
                    $tname = htmlspecialchars($task['task_name']);
                    $status = htmlspecialchars($task['status']);

                    // Handle base64-encoded image
                    $image = handleBase64Upload($task['photo'], $lastTransactionId);

                    // Check if the task already exists for this transaction id
                    $stmtCheckTask = $conn->prepare("SELECT taskid, status FROM task WHERE tid = ? AND tname = ?");
                    $stmtCheckTask->bind_param("is", $lastTransactionId, $tname);
                    $stmtCheckTask->execute();
                    $resultCheckTask = $stmtCheckTask->get_result();
                    $stmtCheckTask->close();

                    if ($resultCheckTask->num_rows > 0) {
                        // Task exists, update the existing task
                        $row = $resultCheckTask->fetch_assoc();
                        $existingTaskId = $row['taskid'];
                        
                        // Check if the status is different, update it
                        if ($row['status'] !== $status) {
                            $stmtUpdateTaskStatus = $conn->prepare("UPDATE task SET status = ? WHERE taskid = ?");
                            $stmtUpdateTaskStatus->bind_param("si", $status, $existingTaskId);
                            $stmtUpdateTaskStatus->execute();
                            $stmtUpdateTaskStatus->close();
                        }

                        // ... (Update other task details if needed)

                    } else {
                        // Task doesn't exist, insert a new task
                        $stmtInsertTask = $conn->prepare("INSERT INTO task (tname, status, image, tid) VALUES (?, ?, ?, ?)");
                        $stmtInsertTask->bind_param("sssi", $tname, $status, $image, $lastTransactionId);
                        $stmtInsertTask->execute();
                        $stmtInsertTask->close();
                    }
                }

                echo "<h2>Data Processed Successfully</h2>";
            } else {
                error_log("Error updating transaction table: " . $stmtUpdateTransaction->error);
            }
        } else {
            echo "Error: 'custid' does not exist in the customer table.";
        }
    } else {
        echo "Error: 'tasks' key is not set or is not an array.";
    }

    $conn->close();
}

function getLastUpdatedTransactionId($conn, $existingCid, $date)
{
    // Fetch the last updated transaction id
    $stmtLastTransaction = $conn->prepare("SELECT tid FROM transaction WHERE custid = ? AND date = ? ORDER BY tid DESC LIMIT 1");
    $stmtLastTransaction->bind_param("is", $existingCid, $date);
    $stmtLastTransaction->execute();
    $resultLastTransaction = $stmtLastTransaction->get_result();
    $stmtLastTransaction->close();

    if ($resultLastTransaction->num_rows > 0) {
        $row = $resultLastTransaction->fetch_assoc();
        return $row['tid'];
    }

    return null;
}
function handleBase64Upload($base64Data, $lastTransactionId)
{
     // Check if the base64 data is present
     if (empty($base64Data)) {
        error_log('Error: No base64 data provided.');
        return null;
    }
    // Set the target directory for base64 uploads
    $targetDir = "uploads/";

    // Generate a unique name for the image

    $fileName = $lastTransactionId . "_" . uniqid() . ".jpeg"; // You can choose a different file format if needed
    $targetFilePath = $targetDir . $fileName;

    // Verify content type and remove data URI header
    $base64Prefix = 'data:image/jpeg;base64,';

    if (strpos($base64Data, $base64Prefix) !== 0) {

        error_log('Error: Invalid base64 data format or unsupported content type.');

        return null;
    }

    // Remove the data URI header

    $base64Data = substr($base64Data, strlen($base64Prefix));
    // Decode the base64 data

    $decodedData = base64_decode($base64Data);

    // Check for decoding errors
   if ($decodedData === false) {
        error_log('Error decoding base64 data.');
        return null;

    }

    // Log the decoded data length for debugging

    error_log('Decoded Data Length: ' . strlen($decodedData));

    // Save the decoded data to the target directory

    $writeResult = file_put_contents($targetFilePath, $decodedData);

    // Check for successful write

    if ($writeResult === false) {
        error_log('Error writing decoded data to file.');

        return null;
    }

    return $targetFilePath;
}

?>