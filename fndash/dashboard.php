<?php
    session_start();
     if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
       exit();
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet" />
    <!-- Add this line to include Font Awesome -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        label {
            width: 80px;
            text-align: right;
            margin-right: 10px;
        }

        input {
            width: 120px;
            padding: 5px;
            box-sizing: border-box;
        }

        .action-column {
            text-align: center;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            background: transparent;
            color: #333;
            cursor: pointer;
        }

        .add-task-container {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .add-task-button {
            width: 120px;
            margin-right: 10px;
        }
         .search-input {
        position: relative;
        margin-right: 10px; /* Adjust spacing between input box and button */
    }

    .search-input input[type="date"] {
        width: 120px;
        padding: 5px;
        box-sizing: border-box;
         margin-right:20px;
    }

    .search-input i {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
    }
    </style>
</head>

<body>
    <div class="header">
        <h2>DASHBOARD</h2>
    </div>

    <div class="search-container">
        <label for="dateSearch">Enter</label>
        <input type="date" id="dateSearch" placeholder="Date">
        <div class="search-input">
        <i class = "fas fa-calendar-alt"></i>
    </div>
        <button type="button" class="btn btn-primary" onclick="showResults()">Search</button>

        <div class="add-task-container justify-content-between">
        <button class="btn btn-primary add-task-button" onclick="addTask()">Add Task</button>
        <button class="btn btn-primary add-task-button" onclick="addCust()">Add Cust</button>
        <a href="#" class="add-task-button" style="padding: 0.25rem 0.5rem; margin-left:480px" onclick="confirmLogout()">
            <i class="fas fa-sign-out-alt" style="font-size: 28px;"></i>
        </a>
        </div>
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Logout Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <a href="login.php" class="btn btn-danger">Yes</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="messageContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this record? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="deleteConfirmed()">Yes, Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> 

    </div>
   

    <!-- Table -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <!-- table content with dummy data -->
    <table id="dashboardTable" class="table">
        <thead class="thead-light">
            <tr>
               <!-- <th>CustId</th>
                <th>CustName</th> -->
                <th>Task Name</th>
                <th>Date</th>
                <th>Status</th>
                <th>Screenshot</th>
               <!-- <th class="action-column">Actions</th>-->
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <script>
        

    ////fetch records by date

         // Function to get the current date in the format YYYY-MM-DD
 function getCurrentDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    var yyyy = today.getFullYear();

    return yyyy + '-' + mm + '-' + dd;
}


  function fetchRecordsForCurrentDate() {
    // Retrieve the current date
    var currentDate = getCurrentDate();

    // Set the current date to the date input field
    $('#dateSearch').val(currentDate);

    // Make an AJAX request to your PHP script to fetch records for the current date
    $.ajax({
        type: 'POST',
        url: 'getRecordsForDate.php', // Replace with your server-side script
        data: { dateSearch: currentDate }, // Ensure that this is correctly set
        dataType: 'json',
        success: function (response) {
            // Update the table with the received data
            updateTable(response);
        },
        error: function (error) {
            console.log('Error fetching data:', error);
        }
    });
}
 fetchRecordsForCurrentDate();

$(document).ready(function () {
    // Call the function to fetch records for the current date on page load
    //fetchRecordsForCurrentDate();

    // Initialize DataTable
    $('#dashboardTable').DataTable({
        "paging": true,
        "pageLength": 6,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
    });
});
  
   // Initialize Bootstrap Datepicker
    
    /////////////////////////////////////////////end//////////////
    /////////////////////////////////
    ///////fetch and display data////
    /////////////////////////////////
/*$(document).ready(function () {
    $('#dashboardTable').DataTable({
        "paging": true,
        "pageLength": 6,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
    });
}); */

function deleteRowConfirmation(row) {
    $('#deleteConfirmationModal').modal('show');

    // Store the row element in a global variable for later use
    window.rowToDelete = row;
}

function deleteConfirmed() {
    var row = window.rowToDelete;

    // Extract the taskid from the data-taskid attribute of the row
    var taskid = $(row).data('taskid');

    // Make an AJAX request to delete the record from the backend
    function deleteRecord(taskid) {
    $.ajax({
        type: 'POST',
        url: 'deleteRecord.php',
        data: { taskid: taskid },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Record deleted successfully
                showAlert('success', response.message);
                // Refresh the table or update the UI as needed
                fetchData(); // Assuming fetchData() retrieves and updates the table data
            } else {
                // Error in record deletion
                showAlert('danger', response.message);
            }
        },
        error: function (error) {
            console.log('Error deleting record:', error);
        }
    });
}

    $('#deleteConfirmationModal').modal('hide');
}

// Attach a click event to the delete button in the table
$('#dashboardTable').on('click', '.btn-delete', function () {
    // Get the taskid from the data-taskid attribute of the button's parent row
    var taskid = $(this).closest('tr').data('taskid');

    // Display a confirmation message
    var confirmDelete = confirm("Are you sure you want to delete this record?");
    if (confirmDelete) {
        // Call the function to delete the record
        deleteRecord(taskid);
    }
});



    function openEditForm(icon) {
    var row = icon.closest('tr');

    // Extract values from the row
    var custId = row.cells[0].innerText;
    var taskName = row.cells[2].innerText;
    var date = row.cells[3].innerText;
    var status = row.cells[4].innerText;

    // Populate the edit form with the extracted data
    document.getElementById('editCid').value = custId;
    document.getElementById('editTaskName').value = taskName;
    document.getElementById('editDate').value = date;

    // Assuming 'editStatusYes' and 'editStatusNo' are radio buttons
    if (status === 'Yes') {
        document.getElementById('editStatusYes').checked = true;
    } else if (status === 'No') {
        document.getElementById('editStatusNo').checked = true;
    }

    // Display the edit form
    document.getElementById('editForm').style.display = 'block';
}

    function displayImagesOnLoad(data) {
        data.forEach(function (record) {
            viewScreenshot(record.taskid);
            viewScreenshot(row.taksid);
        });
    }


    document.addEventListener('DOMContentLoaded', function () {
            // Function to fetch data from the server
            function fetchData() {
                $.ajax({
                    url: 'fetch_data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        populateTable(data);
                    },
                    error: function (error) {
                        console.log('Error fetching data:', error);
                    }
                });
            }

            /////// Function to populate the table with data
         // Function to populate the table with data

   function populateTable(data) {
    var tableBody = document.querySelector('#dashboardTable tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    var currentDate = getCurrentDate(); // Get the current date

  /*  data.forEach(function (row) {
        // Check if the record's date matches the current date
        if (row.status && 
            (row.status.toUpperCase() === 'YES' || row.status.toUpperCase() === 'NO') &&
            row.date === currentDate) {
            
            var newRow = '<tr data-taskid="' + row.taskid + '">' +
                '<td>' + row.tname + '</td>' +
                '<td>' + row.date + '</td>' +
                '<td>' + row.status + '</td>';

            if (row.status.toUpperCase() === 'YES') {
                // Assuming 'row.image' contains the Screenshot data, modify this line accordingly
                newRow += '<td><button class="btn btn-info btn-view" data-taskid="' + row.taskid + '"><i class="fas fa-eye"></i></button></td>';
            } else if (row.status.toUpperCase() === 'NO') {
                // Display "No image found" message for "No" status
                newRow += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + row.taskid + '"><i class="fas fa-eye"></i></button></td>';
            }

            // Append the new row to the table body
            tableBody.innerHTML += newRow;
        }
    }); */

   data.forEach(function (row) {
    // Check if the record's date matches the current date
    if (row.status &&
        (row.status.toUpperCase() === 'YES' || row.status.toUpperCase() === 'NO') &&
        row.date === currentDate) {

        var newRow = '<tr data-taskid="' + row.taskid + '">' +
            '<td>' + row.tname + '</td>' +
            '<td>' + row.date + '</td>' +
            '<td>' + row.status + '</td>';

   /*   if (row.status.toUpperCase() === 'YES') {
            // Check if 'row.image' contains a valid image path
            if (row.image && row.image.trim() !== '') {
                // If image path exists, create the "View" button with eye symbol
                newRow += '<td><button class="btn btn-info btn-view" data-taskid="' + row.taskid + '"><i class="fas fa-eye"></i></button></td>';
            } else {
                // If no image path exists, display "No image found" with eye symbol
                newRow += '<td><button class="btn btn-info btn-view-no-image" onclick="showNoImageMessage(' + row.taskid + ')"><i class="fas fa-eye"></i> </button></td>';
            }
        } else if (row.status.toUpperCase() === 'NO') {
            // Display "No image found" message for "NO" status
            newRow += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + row.taskid + '"><i class="fas fa-eye"></i></button></td>';
        }

        // Append the new row to the table body
        tableBody.innerHTML += newRow;
    }
}); */

if (row.status.toUpperCase() === 'YES') {

    // Check if 'row.image' contains a valid image path
    if (row.image && row.image.trim() !== '') {
        // If image path exists, create the "View" button with eye symbol
        newRow += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + row.taskid + '"><i class="fas fa-eye"></i></button></td>';
    } else {
        // If no image path exists, display "No image found" with eye symbol
        newRow += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + row.taskid + '"><i class="fas fa-eye"></i></button></td>';
    }
} else if (row.status.toUpperCase() === 'NO') {
    // Display "No image found" message for "NO" status
    newRow += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + row.taskid + '"><i class="fas fa-eye"></i></button></td>';
}

// Append the new row to the table body
tableBody.innerHTML += newRow;
}
}); 

    // Display images on page load
// Display images on page load
displayImagesOnLoad(data);

// Attach click event for "Yes" and "No" status icons
$('#dashboardTable').on('click', '.btn-view', function () {
    var taskid = $(this).closest('tr').data('taskid');
    viewScreenshot(taskid);
});

// Attach click event for "No" status icon
$('#dashboardTable').on('click', '.btn-view-no-image', function () {
    var taskid = $(this).closest('tr').data('taskid');
    alert('No image found');
});
}

            // Call the fetchData function when the page loads
            fetchData();
        });
       
        
        ///////////////////////////////////////////////////
        ///search logic , with dispalying data on table////
        ///////////////////////////////////////////////////
/*      function showResults() {
    // Retrieve search parameters
    var custIdSearch = $('#custIdSearch').val();

    // Check if CID is provided
    if (custIdSearch.trim() !== '') {
        // Make an AJAX request to your PHP script
        $.ajax({
            type: 'POST',
            url: 'getSearchResults.php',
            data: { custIdSearch: custIdSearch },
            dataType: 'json',
            success: function (response) {
                // Update the table with the received data
                updateTable(response);
            },
            error: function (error) {
                console.log('Error fetching data:', error);
            }
        });
    } else {
        // Handle the case when CID is not provided
        console.log('Please provide a CID for searching.');
        // You might want to display a message to the user indicating that CID is required.
    }
}*/
       function showResults() {
    // Retrieve search parameters
    var dateSearch = $('#dateSearch').val();

    // Check if CID is provided
    if (dateSearch.trim() !== '') {
        // Make an AJAX request to your PHP script
        $.ajax({
            type: 'POST',
            url: 'getSearchResults.php',
            data: { dateSearch: dateSearch },
            dataType: 'json',
            success: function (response) {
                // Update the table with the received data
                updateTable(response);
            },
            error: function (error) {
                console.log('Error fetching data:', error);
            }
        });
    } else {
        // Handle the case when CID is not provided
        console.log('Please provide a date for searching.');
        // You might want to display a message to the user indicating that CID is required.
    }
}
   


function updateTable(data) {
    // Clear existing table rows
    $('#dashboardTable tbody').empty();

    // Append new rows based on the received data
    data.forEach(function (record) {
        var row = '<tr>';
    /*    row += '<td>' + record.custid + '</td>';
        row += '<td>' + record.name + '</td>'; */
        row += '<td>' + record.tname + '</td>';
        row += '<td>' + record.date + '</td>';
        row += '<td>' + record.status + '</td>';

     /*   if (record.status.toUpperCase() === 'YES') {
            // Assuming 'record.image' contains the Screenshot data, modify this line accordingly
            row += '<td><button class="btn btn-info" onclick="viewScreenshot(' + record.taskid + ')"><i class="fas fa-eye"></i></button></td>';
        } else if (record.status.toUpperCase() === 'NO') {
            // Display "No image found" message for "No" status
            row += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + record.taskid + '" onclick="showNoImageMessage()"><i class="fas fa-eye"></i></button></td>';
        } */
      if (record.status.toUpperCase() === 'YES') {
    // Check if 'record.image' contains a valid image path
    if (record.image && record.image.trim() !== '') {
        // If image path exists, create the "View" button
        row += '<td><button class="btn btn-info" onclick="viewScreenshot(' + record.taskid + ')"><i class="fas fa-eye"></i></button></td>';
    } else {
        // If no image path exists, display "No image found"
        row += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + record.taskid + '" onclick="showNoImageMessage()"><i class="fas fa-eye"></i></button></td>';
    }
} else if (record.status.toUpperCase() === 'NO') {
    // Display "No image found" message for "No" status
    row += '<td><button class="btn btn-info btn-view-no-image" data-taskid="' + record.taskid + '" onclick="showNoImageMessage()"><i class="fas fa-eye"></i></button></td>';
} 





        // Actions column
     /*   row += '<td class="action-column">';
        row += '<button class="btn btn-warning mr-2" onclick="editRow(' + record.custid + ')"><i class="fas fa-edit"></i></button>';
        row += '<button class="btn btn-danger" onclick="deleteRow(this)"><i class="fas fa-trash-alt"></i></button>';
        // Add additional action buttons as needed
        row += '</td>';
        row += '</tr>';*/
        $('#dashboardTable tbody').append(row);
    });

    $('.btn-view').on('click', function () {
        var taskid = $(this).data('taskid');
        viewScreenshot(taskid);
    });
}

// Function to show the "No image found" message modal
function showNoImageMessage() {
    $('#messageContent').text('No image found');
    $('#messageModal').modal('show');
}


    function viewScreenshot(taskid) {
        // Perform an AJAX request to retrieve the image path based on taskid
        //console.log(taskid);
        $.ajax({
            type: 'POST',
            url: 'getImagePath.php',
            data: { taskid: taskid },
            dataType: 'json',
            success: function (response) {
               
                if (response.imagePath) {
                    // If imagePath is found, call the displayImage function with the imagePath
                    displayImage(response.imagePath);
                } else {
                    console.log('Image path not found for taskid: ' + taskid);
                    // You might want to display a message to the user indicating that the image is not available.
                }
            },
            error: function (error) {
                console.log('Error fetching image path:', error);
            }
        });
    }

    // Function to display the image in a modal
    function displayImage(imagePath) {
        // Create an image element and set its source to the imagePath
        var imgElement = document.createElement('img');
        imgElement.src = imagePath;
        imgElement.style.width = '20cm'; // Set the width of the image to 25 cm
        imgElement.style.height = '20cm'; // Set the height of the image to 20 cm
        imgElement.style.objectFit = 'contain';  // Optional: Limit the width of the displayed image

        // Create a modal to display the image
        var modal = document.createElement('div');
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';

        // Append the image element to the modal
        modal.appendChild(imgElement);

        // Attach a click event to close the modal when clicked outside the image
        modal.addEventListener('click', function () {
            modal.style.display = 'none';
        });

        // Append the modal to the body
        document.body.appendChild(modal);
    }

    // Attach a click event to all "View" buttons in the table
    $('#dashboardTable').on('click', '.btn-view', function () {
        // Get the taskid from the data-taskid attribute of the button's parent row
        var taskid = $(this).closest('tr').data('taskid');
        // Call the viewScreenshot function with the taskid
        viewScreenshot(taskid);
    });



//////////////////////////////////////////////////////////////

//////below is the pevious code//////////////////

        function editRow(rowId) {
            window.location.href = 'edit.html?rowId=' + rowId;
            console.log('form clicked');
        }
        
        function deleteRow(button) {
        var confirmDelete = confirm("Are you sure you want to delete this task?");

        if (confirmDelete) {
            var row = button.closest('tr'); // Find the closest parent <tr> element

            if (row) {
                // Remove the row from the table
                row.remove();

                // Add logic to send an AJAX request or perform server-side deletion
                // You may need to extract the rowId from the row and pass it to the server

                console.log('Task deleted successfully');
            } else {
                console.log('Row not found');
            }
        } else {
            console.log('Deletion canceled by the user');
        }
    }


        function myFunction() {
            console.log('form clicked');
        }

        function addTask() {
            window.location.href = 'test1.html';
            console.log('form clicked');            
        }

    function addCust() {
        window.location.href = 'customer.html';
        console.log('Add Cust button clicked');
    }

    function confirmLogout() {
            $('#logoutModal').modal('show');
        }
    </script>
</body>
</html>