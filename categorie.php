<?php
  $page_title = 'All categories';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  // page_require_level(1);
  
  $all_categories = find_all('categories')
?>
<?php
 if(isset($_POST['add_cat'])){
   $req_field = array('categorie-name');
   validate_fields($req_field);
   $cat_name = remove_junk($db->escape($_POST['categorie-name']));
   if(empty($errors)){
      $sql  = "INSERT INTO categories (name)";
      $sql .= " VALUES ('{$cat_name}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added New Category");
        redirect('categorie.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('categorie.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('categorie.php',false);
   }
 }
?>
<?php include_once('layouts/header.php'); ?>

  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
  </div>
   <div class="row">
    <div class="col-md-5">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Category</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="categorie.php">
            <div class="form-group">
                <input type="text" class="form-control" name="categorie-name" placeholder="Category Name">
            </div>
            <button type="submit" name="add_cat" class="btn btn-primary">Add Category</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Categories</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Categories</th>
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($all_categories as $cat):?>
                <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td><?php echo remove_junk(ucfirst($cat['name'])); ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                       <button 
                          class="btn btn-xs btn-warning btn-edit-cat" 
                          data-id="<?php echo (int)$cat['id']; ?>" 
                          data-name="<?php echo remove_junk(ucfirst($cat['name'])); ?>" 
                          data-toggle="modal" 
                          data-target="#editCategorieModal" 
                          title="Edit">
                          <span class="glyphicon glyphicon-edit"></span>
                        </button>


                        <a href="delete_categorie.php?id=<?php echo (int)$cat['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                          <span class="glyphicon glyphicon-trash"></span>
                        </a>
                      </div>
                    </td>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
       </div>
    </div>
    </div>
   </div>
  </div>


<!-- ==================== EDIT CATEGORY MODAL ==================== -->
<div class="modal fade" id="editCategorieModal" tabindex="-1" role="dialog" aria-labelledby="editCategorieModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title" id="editCategorieModalLabel">
          <span class="glyphicon glyphicon-th"></span> Edit Category
        </h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <form id="frmEditCategorie">
          <input type="hidden" name="id" id="edit-cat-id">
          <div class="form-group">
            <label for="edit-cat-name">Category Name</label>
            <input type="text" class="form-control" id="edit-cat-name" name="categorie-name">
          </div>
        </form>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <span class="glyphicon glyphicon-remove"></span> Cancel
        </button>
        <button type="submit" class="btn btn-primary" id="btnUpdateCategorie">
          <span class="glyphicon glyphicon-ok"></span> Update
        </button>
      </div>

    </div>
  </div>
</div>




  <?php include_once('layouts/footer.php'); ?>

<script>
  // When clicking edit button
$(document).on('click', '.btn-edit-cat', function() {
  const id = $(this).data('id');
  const name = $(this).data('name');
  
  // Fill modal fields
  $('#edit-cat-id').val(id);
  $('#edit-cat-name').val(name);
});




$('#btnUpdateCategorie').on('click', function(e) {
  e.preventDefault();

  const formData = new FormData($('#frmEditCategorie')[0]);
  formData.append('requestType', 'update_categorie');

  $.ajax({
    type: 'POST',
    url: 'controller.php',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function(response) {
      if (response.status === 200) {
        $('#editCategorieModal').modal('hide');

        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: response.message,
          showConfirmButton: false,
          timer: 1500
        }).then(() => {
          location.reload(); // Refresh table or data
        });

      } else {
        Swal.fire({
          icon: 'error',
          title: 'Update Failed',
          text: response.message
        });
      }
    }
  });
});


</script>