<?php
  $page_title = 'All Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  //  page_require_level(2);
   $all_categories = find_all('categories');
  $all_photo = find_all('media');
  $products = join_product_table();
?>
<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
         <div class="pull-right">
           <button class="btn btn-success" data-toggle="modal" data-target="#addProductModal">
            <span class="glyphicon glyphicon-plus"></span> Add New Product
          </button>

         </div>
        </div>
        <div class="panel-body">
          <table id="productTable" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Photo</th>
                    <th>Product Title</th>
                    <th class="text-center" style="width: 10%;">Categories</th>
                    <th class="text-center" style="width: 10%;">In-Stock</th>
                    <th class="text-center" style="width: 10%;">Buying Price</th>
                    <th class="text-center" style="width: 10%;">Selling Price</th>
                    <th class="text-center" style="width: 10%;">Product Added</th>
                    <th class="text-center" style="width: 100px;">QR Code</th> <!-- New QR Code Column -->
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="text-center"><?php echo count_id(); ?></td>
                    <td>
                        <?php if($product['media_id'] === '0'): ?>
                            <img class="img-avatar img-circle" src="uploads/products/no_image.png" alt="">
                        <?php else: ?>
                            <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?php echo remove_junk($product['name']); ?></td>
                    <td class="text-center"><?php echo remove_junk($product['categorie']); ?></td>
                    <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                    <td class="text-center"><?php echo remove_junk($product['buy_price']); ?></td>
                    <td class="text-center"><?php echo remove_junk($product['sale_price']); ?></td>
                    <td class="text-center"><?php echo read_date($product['date']); ?></td>
                    <td class="text-center"> <!-- QR Code -->
                        <?php
                        $qrPath = 'qr_codes/' . $product['id'] . '.png';
                        if (file_exists($qrPath)):
                        ?>
                            <img 
                                src="<?php echo $qrPath; ?>" 
                                alt="QR Code" 
                                class="qr-thumbnail" 
                                style="width:50px; height:50px; cursor:pointer;"
                                data-qr="<?php echo $qrPath; ?>"
                            >
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-xs btn-warning btn-edit" data-id="<?= $product['id']; ?>">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </button>
                            <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs" title="Delete" data-toggle="tooltip">
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





  







  <!-- ==================== ADD PRODUCT MODAL ==================== -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"> <!-- modal-lg for wider layout -->
    <div class="modal-content">
      
      <!-- Modal Header -->
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title" id="addProductModalLabel">
          <span class="glyphicon glyphicon-th"></span> Add New Product
        </h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form id="frmAddProduct" method="post" action="add_product.php" class="clearfix">

          <!-- Product Title -->
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-th-large"></i>
              </span>
              <input type="text" class="form-control" name="product-title" placeholder="Product Title">
            </div>
          </div>

          <!-- Category & Photo -->
          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <select class="form-control" name="product-categorie">
                  <option value="">Select Product Category</option>
                  <?php foreach ($all_categories as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>">
                      <?php echo remove_junk($cat['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <select class="form-control" name="product-photo">
                  <option value="">Select Product Photo</option>
                  <?php foreach ($all_photo as $photo): ?>
                    <option value="<?php echo (int)$photo['id']; ?>">
                      <?php echo remove_junk($photo['file_name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Quantity, Buying & Selling Price -->
          <div class="form-group">
            <div class="row">
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-shopping-cart"></i>
                  </span>
                  <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity">
                </div>
              </div>

              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-usd"></i>
                  </span>
                  <input type="number" class="form-control" name="buying-price" placeholder="Buying Price">
                  <span class="input-group-addon">.00</span>
                </div>
              </div>

              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-usd"></i>
                  </span>
                  <input type="number" class="form-control" name="selling-price" placeholder="Selling Price">
                  <span class="input-group-addon">.00</span>
                </div>
              </div>
            </div>
          </div>

        </form>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <span class="glyphicon glyphicon-remove"></span> Cancel
        </button>
        <button type="submit" form="frmAddProduct" name="add_product" class="btn btn-danger">
          <span class="glyphicon glyphicon-plus"></span> Add Product
        </button>
      </div>

    </div>
  </div>
</div>













<!-- ==================== EDIT PRODUCT MODAL ==================== -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
        <h4 class="modal-title" id="editProductModalLabel">
          <span class="glyphicon glyphicon-th"></span> Edit Product
        </h4>
      </div>

      <div class="modal-body">
        <form id="frmEditProduct" method="post">

          <input type="hidden" id="edit-product-id" name="product-id">

          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-th-large"></i>
              </span>
              <input type="text" class="form-control" name="product-title" id="edit-product-title" placeholder="Product Name">
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <select class="form-control" name="product-categorie" id="edit-product-categorie">
                  <option value="">Select a category</option>
                </select>
              </div>
              <div class="col-md-6">
                <select class="form-control" name="product-photo" id="edit-product-photo">
                  <option value="">No image</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-4">
                <label for="edit-product-quantity">Quantity</label>
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-shopping-cart"></i>
                  </span>
                  <input type="number" class="form-control" name="product-quantity" id="edit-product-quantity">
                </div>
              </div>

              <div class="col-md-4">
                <label for="edit-buying-price">Buying Price</label>
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-usd"></i>
                  </span>
                  <input type="number" class="form-control" name="buying-price" id="edit-buying-price">
                  <span class="input-group-addon">.00</span>
                </div>
              </div>

              <div class="col-md-4">
                <label for="edit-selling-price">Selling Price</label>
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="glyphicon glyphicon-usd"></i>
                  </span>
                  <input type="number" class="form-control" name="selling-price" id="edit-selling-price">
                  <span class="input-group-addon">.00</span>
                </div>
              </div>
            </div>
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" form="frmEditProduct" class="btn btn-danger">Update Product</button>
      </div>

    </div>
  </div>
</div>





<!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
        
      </div>
      <div class="modal-body text-center">
        <img src="" id="qrModalImage" style="max-width:100%; height:auto;">
      </div>
    </div>
  </div>
</div>







  <?php include_once('layouts/footer.php'); ?>

<script>


$(document).ready(function() {
    $('#productTable').DataTable({
        "order": [[ 0, "asc" ]], // Default sort by first column
        "columnDefs": [
            { "orderable": false, "targets": [1, 8, 9] } // Disable sorting on Photo, QR, Actions
        ]
    });
});





 $(document).on('click', '.qr-thumbnail', function() {
        var qrSrc = $(this).data('qr');          // Get QR image path
        $('#qrModalImage').attr('src', qrSrc);   // Set the modal image src
        $('#qrModal').modal('show');             // Show the modal
    });





    /* ==================== ADD PRODUCT ==================== */
$('#frmAddProduct').on('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('requestType', 'add_product');

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
        $('#addProductModal').modal('hide');
        $('#frmAddProduct')[0].reset();

        setTimeout(() => {
          location.reload();
        }, 2000);
      }
    }
  });
});










/* ==================== EDIT PRODUCT - OPEN MODAL ==================== */
$('.btn-edit').on('click', function() {
  const id = $(this).data('id');

  $.ajax({
    type: 'GET',
    url: 'controller.php',
    data: { requestType: 'get_product', id: id },
    dataType: 'json', // ✅ correct for expecting JSON response
   success: function(data) {
      if (data.status === 200) {
        const p = data.product;

        $('#editProductModal').modal('show');
        $('#edit-product-id').val(p.id);
        $('#edit-product-title').val(p.name);
        $('#edit-product-quantity').val(p.quantity);
        $('#edit-buying-price').val(p.buy_price);
        $('#edit-selling-price').val(p.sale_price);

        // Populate categories
        const $catSelect = $('#edit-product-categorie');
        $catSelect.empty().append('<option value="">Select a category</option>');
        data.all_categories.forEach(cat => {
          const selected = cat.id == p.categorie_id ? 'selected' : '';
          $catSelect.append(`<option value="${cat.id}" ${selected}>${cat.name}</option>`);
        });

        // Populate photos
        const $photoSelect = $('#edit-product-photo');
        $photoSelect.empty().append('<option value="">No image</option>');
        data.all_photo.forEach(photo => {
          const selected = photo.id == p.media_id ? 'selected' : '';
          $photoSelect.append(`<option value="${photo.id}" ${selected}>${photo.file_name}</option>`);
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message
        });
      }
    }
  });
});






/* ==================== EDIT USER - SUBMIT ==================== */
$('#frmEditProduct').on('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('requestType', 'update_product');

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
        $('#editProductModal').modal('hide');
        $('#frmEditProduct')[0].reset();

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