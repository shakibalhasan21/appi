<?php
session_start();
if ($_SESSION['uid'] != "1") {
  header('location:logout.php');
} else {

  $user_id = $_SESSION['uid'];
  include_once("function.php");
  $fetchdata = new DB_con();
  $obj = new DB_con();

?>
  <?php include('includes/head.php');
  ?>


<style>
  .short-text, .full-text {
    display: inline-block;
    word-wrap: break-word;
}

.btn-link {
    padding: 0;
    font-size: 0.9rem;
    text-decoration: underline;
    cursor: pointer;
}

</style>

  <main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
        <h1>API List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">API List</li>
            </ol>
        </nav>
    </div>
    <a class="btn btn-success rounded-pill add_new" href="add_apis.php" style="min-width: 106px;">
        <i class="bi bi-plus-circle"></i>Add New
    </a>
</div><!-- End Page Title -->


    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center">All API List</h5>

              <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col"> # </th>
                      <th scope="col">Name</th>
                      <th scope="col">Api Url</th>
                      <th scope="col">Api Key</th>
                      <th scope="col">Created</th>
                      <th scope="col">Updated</th>
                      <!-- <th scope="col"> Add Balance</th> -->
                      <th scope="col"> Action</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    <?php
                    $sql = $obj->fetch_apis();

                    // $pin = $obj->pin_api();
                    // echo $pin;
                    $cnt = 1;
                    while ($row = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <th scope="row"> <?php echo $cnt; ?> </th>
                            <td><?php echo $row['api_name']; ?></td>
                            <td>
                                <span class="short-text"><?php echo substr($row['api_url'], 0, 10); ?>...</span>
                                <span class="full-text d-none"><?php echo $row['api_url']; ?></span>
                                <button type="button" class="btn btn-link toggle-text">Show More</button>
                            </td>
                            <td>
                                <span class="short-text"><?php echo substr($row['api_key'], 0, 10); ?>...</span>
                                <span class="full-text d-none"><?php echo $row['api_key']; ?></span>
                                <button type="button" class="btn btn-link toggle-text">Show More</button>
                            </td>

                              <?php
                              $created_at = $row['created_at']; 
                              $updated_at = $row['updated_at']; 
                              $created_datetime = new DateTime($created_at);
                              $updated_datetime = new DateTime($updated_at);

                              $formatCreated_at = $created_datetime->format('d/m/Y h:i A');
                              $formatUpdated_at = $updated_datetime->format('d/m/Y h:i A');
                              // echo $formattedDate; // Outputs: 19/12/2024 02:04 PM
                              ?>

                            <td><?php echo $formatCreated_at; ?></td>
                            <td><?php echo $formatUpdated_at; ?></td>
                            <td> 
                            <li class="list-inline-item">
                                  <!-- Edit Button -->
                                  <button class="btn btn-success btn-sm rounded-3 edit-btn" 
                                    type="button" 
                                    title="Edit"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-api-name="<?php echo $row['api_name']; ?>"
                                    data-api-url="<?php echo $row['api_url']; ?>"
                                    data-api-key="<?php echo $row['api_key']; ?>"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>


                                <!-- Delete Button -->
                                <a href="delete_api.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm rounded-3 mt-1" title="Delete">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </li>

                                
                            </td>
                        </tr>
                    <?php
                        $cnt = $cnt + 1;
                    } ?>
                </tbody>


              </table>



            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit API</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editApiName" class="form-label">API Name</label>
                        <input type="text" class="form-control" id="editApiName" name="api_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editApiUrl" class="form-label">API URL</label>
                        <input type="text" class="form-control" id="editApiUrl" name="api_url" required>
                    </div>
                    <div class="mb-3">
                        <label for="editApiKey" class="form-label">API Key</label>
                        <input type="text" class="form-control" id="editApiKey" name="api_key" required>
                    </div>
                    <input type="hidden" id="editApiId" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


  <script>

document.addEventListener("DOMContentLoaded", function () {
    const toggleButtons = document.querySelectorAll(".toggle-text");

    toggleButtons.forEach(button => {
        button.addEventListener("click", function () {
            const parent = this.closest("td");
            const shortText = parent.querySelector(".short-text");
            const fullText = parent.querySelector(".full-text");

            if (shortText.classList.contains("d-none")) {
                shortText.classList.remove("d-none");
                fullText.classList.add("d-none");
                this.textContent = "Show More";
            } else {
                shortText.classList.add("d-none");
                fullText.classList.remove("d-none");
                this.textContent = "Show Less";
            }
        });
    });
});




document.addEventListener('DOMContentLoaded', () => {
    const editButtons = document.querySelectorAll('.edit-btn');
    const editForm = document.getElementById('editForm');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Populate the modal fields with data from the button's data attributes
            const id = button.getAttribute('data-id');
            const apiName = button.getAttribute('data-api-name');
            const apiUrl = button.getAttribute('data-api-url');
            const apiKey = button.getAttribute('data-api-key');

            document.getElementById('editApiId').value = id;
            document.getElementById('editApiName').value = apiName;
            document.getElementById('editApiUrl').value = apiUrl;
            document.getElementById('editApiKey').value = apiKey;
        });
    });

    // Handle form submission
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(editForm);

        try {
            const response = await fetch('update_api.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json(); // Expect JSON response from the server

            if (result.success) {

                location.reload(); // Reload the page to reflect changes
            } else {
                alert('Error updating API: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        }
    });
});




  </script>



  
  <?php include('includes/footer.php');
  ?>
<?php } ?>