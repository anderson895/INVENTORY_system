<?php
$page_title = 'All Users';
require_once('includes/load.php');
// page_require_level(1);
$all_users = find_all_user();
$groups = find_all('user_groups');
include_once('layouts/header.php');
?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong><span class="glyphicon glyphicon-th"></span> <span>Users</span></strong>
        <button class="btn btn-info pull-right" data-toggle="modal" data-target="#addUserModal">Add New User</button>
      </div>

      <div class="panel-body">
        <table class="table table-bordered table-striped" id="userTable">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Name</th>
              <th>Username</th>
              <th>Email</th>
              <th class="text-center">Role</th>
              <th class="text-center">Status</th>
              <th>Last Login</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_users as $a_user): ?>
              <tr data-id="<?= $a_user['id']; ?>">
                <td class="text-center"><?php echo count_id();?></td>
                <td><?= remove_junk(ucwords($a_user['name'])) ?></td>
                <td><?= remove_junk($a_user['username']) ?></td>
                <td><?= remove_junk($a_user['email']) ?></td>
                <td class="text-center"><?= remove_junk(ucwords($a_user['group_name'])) ?></td>
                <td class="text-center">
                  <?php if ($a_user['status'] === '1'): ?>
                    <span class="label label-success">Active</span>
                  <?php else: ?>
                    <span class="label label-danger">Inactive</span>
                  <?php endif; ?>
                </td>
                <td><?= read_date($a_user['last_login']) ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <button class="btn btn-xs btn-warning btn-edit" data-id="<?= $a_user['id']; ?>"><i class="glyphicon glyphicon-pencil"></i></button>
                    <a href="delete_user.php?id=<?= (int)$a_user['id']; ?>" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
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

<!-- ADD USER MODAL -->
<div id="addUserModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="frmAddUser">
        <div class="modal-header">
          <h4 class="modal-title">Add New User</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" class="form-control" name="full-name" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="form-group">
            <label>User Role</label>
            <select class="form-control" name="level" required>
              <?php foreach ($groups as $group): ?>
                <option value="<?= $group['group_level']; ?>"><?= ucwords($group['group_name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="frmEditUser">
        <div class="modal-header">
          <h4 class="modal-title">Edit User</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="edit-user-id">
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name" id="edit-name" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" id="edit-username" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" id="edit-email" required>
          </div>

          <div class="form-group">
            <label>User Role</label>
            <select class="form-control" name="level" id="edit-level" required>
              <?php foreach ($groups as $group): ?>
                <option value="<?= $group['group_level']; ?>"><?= ucwords($group['group_name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status" id="edit-status">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php include_once('layouts/footer.php'); ?>

<script>
/* ==================== ADD USER ==================== */
$('#frmAddUser').on('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('requestType', 'add_user');

  $.ajax({
    type: 'POST',
    url: 'controller.php',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function(data) {
      Swal.fire({
        icon: data.status === 200 ? 'success' : 'error',
        title: data.status === 200 ? 'Success' : 'Error',
        text: data.message,
        timer: 2000,
        showConfirmButton: false
      });

      if (data.status === 200) {
        $('#addUserModal').modal('hide');
        $('#frmAddUser')[0].reset();

        setTimeout(() => {
          location.reload();
        }, 2000);
      }
    }
  });
});



/* ==================== EDIT USER - OPEN MODAL ==================== */
$('.btn-edit').on('click', function() {
  const id = $(this).data('id');

  $.ajax({
    type: 'GET',
    url: 'controller.php',
    data: { requestType: 'get_user', id: id },
    dataType: 'json', // ✅ correct key for expecting JSON
    success: function(data) {
      if (data.status === 200) {
        $('#edit-user-id').val(data.user.id);
        $('#edit-name').val(data.user.name);
        $('#edit-username').val(data.user.username);
        $('#edit-email').val(data.user.email);
        $('#edit-level').val(data.user.user_level);
        $('#edit-status').val(data.user.status);
        $('#editUserModal').modal('show');
      } else {
        alert(data.message || 'Failed to fetch user data.');
      }
    },
    error: function(xhr, status, error) {
      console.error('AJAX Error:', error);
      alert('An error occurred while fetching user details.');
    }
  });
});




/* ==================== EDIT USER - SUBMIT ==================== */
$('#frmEditUser').on('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('requestType', 'update_user');

  $.ajax({
    type: 'POST',
    url: 'controller.php',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json', // ✅ Automatically parse JSON response
    success: function(data) {
      Swal.fire({
        icon: data.status === 200 ? 'success' : 'error',
        title: data.status === 200 ? 'Updated Successfully' : 'Update Failed',
        text: data.message,
        timer: 2000,
        showConfirmButton: false
      });

      if (data.status === 200) {
        $('#editUserModal').modal('hide');
        $('#frmEditUser')[0].reset();

        // Delay reload to show success message
        setTimeout(() => {
          location.reload();
        }, 2000);
      }
    },
    error: function(xhr, status, error) {
      console.error('AJAX Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Request Failed',
        text: 'An error occurred while updating the user.',
      });
    }
  });
});
</script>