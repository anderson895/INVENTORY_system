<?php
  $page_title = 'All Group';
  require_once('includes/load.php');
  // page_require_level(1);
  $all_groups = find_all('user_groups');
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Groups</span>
        </strong>
        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-info pull-right btn-sm" data-toggle="modal" data-target="#addGroupModal">
          Add New Group
        </button>
      </div>

      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th>Group Name</th>
                <th class="text-center" style="width: 20%;">Group Level</th>
                <th class="text-center" style="width: 15%;">Status</th>
                <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($all_groups as $a_group): ?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo remove_junk(ucwords($a_group['group_name']))?></td>
                <td class="text-center"><?php echo remove_junk(ucwords($a_group['group_level']))?></td>
                <td class="text-center">
                  <?php if($a_group['group_status'] === '1'): ?>
                    <span class="label label-success">Active</span>
                  <?php else: ?>
                    <span class="label label-danger">Deactive</span>
                  <?php endif;?>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <button class="btn btn-xs btn-warning btnEditGroup" 
                            data-id="<?php echo (int)$a_group['id'];?>"
                            data-name="<?php echo remove_junk($a_group['group_name']);?>"
                            data-level="<?php echo remove_junk($a_group['group_level']);?>"
                            data-status="<?php echo remove_junk($a_group['group_status']);?>"
                            title="Edit">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </button>
                    <a href="delete_group.php?id=<?php echo (int)$a_group['id'];?>" class="btn btn-xs btn-danger" title="Remove" onclick="return confirm('Are you sure you want to delete this group?');">
                      <i class="glyphicon glyphicon-remove"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<!-- ADD GROUP MODAL -->
<div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="frmAdd_group" method="post">
        <div class="spinner-border text-primary spinner" role="status" style="display:none;">
          <span class="sr-only">Loading...</span>
        </div>

        <div class="modal-header">
          <h4 class="modal-title" id="addGroupModalLabel">Add New Group</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Group Name</label>
            <input type="text" class="form-control" name="group-name" required>
          </div>
          <div class="form-group">
            <label>Group Level</label>
            <input type="number" class="form-control" name="group-level" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status">
              <option value="1">Active</option>
              <option value="0">Deactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" name="add_group" class="btn btn-success">Add Group</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT GROUP MODAL -->
<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-labelledby="editGroupModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="frmEdit_group" method="post">
        <input type="hidden" name="group-id" id="edit-group-id">

        <div class="spinner-border text-primary spinner" role="status" style="display:none;">
          <span class="sr-only">Loading...</span>
        </div>

        <div class="modal-header">
          <h4 class="modal-title" id="editGroupModalLabel">Edit Group</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Group Name</label>
            <input type="text" class="form-control" id="edit-group-name" name="group-name" required>
          </div>
          <div class="form-group">
            <label>Group Level</label>
            <input type="number" class="form-control" id="edit-group-level" name="group-level" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" id="edit-group-status" name="status">
              <option value="1">Active</option>
              <option value="0">Deactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" name="update_group" class="btn btn-info">Update Group</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {

  /* ================= ADD GROUP ================= */
$('#frmAdd_group').on('submit', function(e) {
  e.preventDefault();
  $('.spinner').show();
  const $submitBtn = $(this).find('button[type="submit"]');
  $submitBtn.prop('disabled', true);

  const formData = new FormData(this);
  formData.append('requestType', 'Add_group');

  $.ajax({
    type: "POST",
    url: "controller.php",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(response) {
      $('.spinner').hide();
      $submitBtn.prop('disabled', false);

      if (response.status === 200) {
        Swal.fire('Success!', response.message, 'success').then(() => location.reload());
      } else {
        Swal.fire('Error', response.message || 'Something went wrong.', 'error');
      }
    }
  });
});

/* ================= EDIT GROUP ================= */
$('.btnEditGroup').click(function() {
  $('#edit-group-id').val($(this).data('id'));
  $('#edit-group-name').val($(this).data('name'));
  $('#edit-group-level').val($(this).data('level'));
  $('#edit-group-status').val($(this).data('status'));
  $('#editGroupModal').modal('show');
});

$('#frmEdit_group').on('submit', function(e) {
  e.preventDefault();
  $('.spinner').show();
  const $submitBtn = $(this).find('button[type="submit"]');
  $submitBtn.prop('disabled', true);

  const formData = new FormData(this);
  formData.append('requestType', 'Update_group');

  $.ajax({
    type: "POST",
    url: "controller.php", // same backend file
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(response) {
      $('.spinner').hide();
      $submitBtn.prop('disabled', false);

      if (response.status === 200) {
        Swal.fire('Updated!', response.message, 'success').then(() => location.reload());
      } else {
        Swal.fire('Error', response.message || 'Failed to update group.', 'error');
      }
    }
  });
});


});
</script>
