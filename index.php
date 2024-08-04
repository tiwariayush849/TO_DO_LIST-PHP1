<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "notes";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit'])) {
        // Update the record
        $sno = $_POST['snoEdit'];
        $title = mysqli_real_escape_string($conn, $_POST["titleEdit"]);
        $description = mysqli_real_escape_string($conn, $_POST["descriptionEdit"]);
        $sql = "UPDATE `notes` SET `title`='$title', `description`='$description' WHERE `sno`=$sno";
        if (mysqli_query($conn, $sql)) {
            echo "Note updated successfully";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Insert the new record
        $title = mysqli_real_escape_string($conn, $_POST["title"]);
        $description = mysqli_real_escape_string($conn, $_POST["description"]);
        $sql = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')";
        if (mysqli_query($conn, $sql)) {
            echo "New note added successfully";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Handle delete operation
if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $sql = "DELETE FROM `notes` WHERE `sno`=$sno";
    if (mysqli_query($conn, $sql)) {
        echo "Note deleted successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch data for display
$sql = "SELECT * FROM `notes`";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP-CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.0/css/jquery.dataTables.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">PHP-CRUD</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Contact Us</a>
              </li>
            </ul>
            <form class="d-flex" role="search">
              <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
          </div>
        </div>
      </nav>
      <div class="container my-4">
        <h3>Add a Note</h3>
        <form action="index.php" method="post">
            <div class="mb-3">
              <label for="title" class="form-label">Note Title</label>
              <input type="text" class="form-control" id="title" name="title">
            </div>
            <div class="form-floating mb-3">
              <textarea class="form-control" name="description" id="description" style="height: 100px" required></textarea>
              <label for="description">Note Description</label>
            </div>
            <button type="submit" class="btn btn-primary">Add Note</button>
        </form>
      </div>
      <div class="container mb-4">
        <h3>Your Notes</h3>
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">S.NO</th>
                    <th scope="col">TITLE</th>
                    <th scope="col">DESCRIPTION</th>
                    <th scope="col">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $sno=0;
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $sno= $sno+1;
                            echo "<tr>";
                            echo "<th scope='row'>" . $sno . "</th>";
                            echo "<td>" . $row['title'] . "</td>";
                            echo "<td>" . $row['description'] . "</td>";
                            echo "<td>
                                    <button class='btn btn-sm btn-warning edit' data-id='" . $row['sno'] . "' data-title='" . $row['title'] . "' data-description='" . $row['description'] . "'>Edit</button>
                                    <a href='index.php?delete=" . $row['sno'] . "' class='btn btn-sm btn-danger'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Error: " . mysqli_error($conn) . "</td></tr>";
                    }
                ?> 
            </tbody>
        </table>
      </div>

      <!-- Edit Modal -->
      <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Edit Note</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="snoEdit" id="snoEdit">
                    <div class="mb-3">
                        <label for="titleEdit" class="form-label">Note Title</label>
                        <input type="text" class="form-control" id="titleEdit" name="titleEdit">
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" name="descriptionEdit" id="descriptionEdit" style="height: 100px" required></textarea>
                        <label for="descriptionEdit">Note Description</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
          </div>
        </div>
      </div>

      <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.datatables.net/1.13.0/js/jquery.dataTables.min.js"></script>
      <script>
          $(document).ready(function () {
              $('#myTable').DataTable();

              // Edit button click handler
              $('.edit').click(function () {
                  var sno = $(this).data('id');
                  var title = $(this).data('title');
                  var description = $(this).data('description');
                  $('#snoEdit').val(sno);
                  $('#titleEdit').val(title);
                  $('#descriptionEdit').val(description);
                  $('#editModal').modal('show');
              });
          });
      </script>
</body>
</html>