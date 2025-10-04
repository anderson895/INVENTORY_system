<?php
  $page_title = 'All sale';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php
$sales = find_all_sale();
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>All Sales</span>
          </strong>
          <div class="pull-right">
           <!-- Trigger Modal Button -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#saleModal">
              ADD SALE
            </button>

          </div>
        </div>
        <div class="panel-body">
          <table id="datatable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th> Product name </th>
                <th class="text-center" style="width: 15%;"> Quantity</th>
                <th class="text-center" style="width: 15%;"> Total </th>
                <th class="text-center" style="width: 15%;"> Date </th>
                <th class="text-center" style="width: 100px;"> Actions </th>
             </tr>
            </thead>
           <tbody>
             <?php foreach ($sales as $sale):?>
             <tr>
               <td class="text-center"><?php echo count_id();?></td>
               <td><?php echo remove_junk($sale['name']); ?></td>
               <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
               <td class="text-center"><?php echo remove_junk($sale['price']); ?></td>
               <td class="text-center"><?php echo $sale['date']; ?></td>
               <td class="text-center">
                  <div class="btn-group">
                     <a href="edit_sale.php?id=<?php echo (int)$sale['id'];?>" class="btn btn-warning btn-xs"  title="Edit" data-toggle="tooltip">
                       <span class="glyphicon glyphicon-edit"></span>
                     </a>
                     <a href="delete_sale.php?id=<?php echo (int)$sale['id'];?>" class="btn btn-danger btn-xs"  title="Delete" data-toggle="tooltip">
                       <span class="glyphicon glyphicon-trash"></span>
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















<!-- Modal -->
<div class="modal fade" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="saleModalLabel">
  <div class="modal-dialog modal-lg" role="document"> <!-- modal-lg or modal-xl depends on your Bootstrap version -->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="saleModalLabel">Sale Edit</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <!-- Search Form -->
        <?php echo display_msg($msg); ?>
        <form method="post" action="ajax.php" autocomplete="off" id="sug-form" class="mb-3">
          <div class="input-group">
            <input type="text" id="sug_input" class="form-control" name="title" placeholder="Search for product name">
            <span class="input-group-btn">
              <button type="submit" class="btn btn-primary">Find It</button>
            </span>
          </div>
          <div id="result" class="list-group mt-2"></div>
        </form>

        <!-- Sale Edit Table -->
        <form id="frmAdd_sale" method="post">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="product_info"></tbody>
          </table>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



  <?php include_once('layouts/footer.php'); ?>


<script>
  $(document).ready(function() {
    $('#datatable').DataTable({
        "order": [[ 0, "asc" ]], // Default sort by first column
        
    });
});





    /* ==================== ADD SALE ==================== */
$('#frmAdd_sale').on('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('requestType', 'add_sale');

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
        $('#saleModal').modal('hide');
        $('#frmAdd_sale')[0].reset();

        setTimeout(() => {
          location.reload();
        }, 2000);
      }
    }
  });
});





</script>