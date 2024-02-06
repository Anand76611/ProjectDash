
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

 

    // Check if 'tasks' key is set and is an array

    if (isset($decodedData["tasks"]) && is_array($decodedData["tasks"])) {

        $taskDetails = $decodedData["tasks"];

 

        // Insert customer information

        $stmtCust = $conn->prepare("INSERT INTO customer(custid, name) VALUES (?, ?)");

        $stmtCust->bind_param("is", $existingCid, $name);

        $resultCust = $stmtCust->execute();

 

        if ($resultCust) {

            // Insert transaction information

            $stmtTransaction = $conn->prepare("INSERT INTO transaction (custid, date) VALUES (?, ?)");

            $stmtTransaction->bind_param("is", $existingCid, $date);

            $resultTransaction = $stmtTransaction->execute();

 

            if ($resultTransaction) {

                $lastTransactionId = $conn->insert_id;

 

                foreach ($taskDetails as $task) {

                    $tname = $task['task_name'];

                    $status = $task['status'];

 

                    // Handle base64-encoded image

                    $image = handleBase64Upload($task['photo'], $lastTransactionId);

 

                    // Insert task information

                    $stmtTask = $conn->prepare("INSERT INTO task (tname, status, image, tid) VALUES (?, ?, ?, ?)");

                    $stmtTask->bind_param("sssi", $tname, $status, $image, $lastTransactionId);

                    $resultTask = $stmtTask->execute();

 

                    if (!$resultTask) {

                        error_log("Error inserting into task table: " . $stmtTask->error);

                    }

 

                    $stmtTask->close();

                }

 

                echo "<h2>Data Processed Successfully</h2>";

            } else {

                error_log("Error inserting into transaction table: " . $stmtTransaction->error);

            }

        } else {

            error_log("Error inserting into customer table: " . $stmtCust->error);

        }

 

        $stmtCust->close();

        $stmtTransaction->close();

    } else {

        echo "Error: 'tasks' key is not set or is not an array.";

    }

 

    $conn->close();

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

 

    // Return the path to the uploaded image

    return $targetFilePath;

}


?>


