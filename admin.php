<?php
  $page_title = 'Admin Home Page';
  require_once('includes/load.php');
  page_require_level(1);

  $c_categorie     = count_by_id('categories');
  $c_product       = count_by_id('products');
  $c_sale          = count_by_id('sales');
  $c_user          = count_by_id('users');
  $products_sold   = find_higest_saleing_product('10');
  $recent_products = find_recent_product_added('5');
  $recent_sales    = find_recent_sale_added('5');
?>
<?php include_once('layouts/header.php'); ?>

<!-- Display messages -->
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Summary Panels -->
<div class="row">
  <a href="users.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix" style="height:120px; display:flex; align-items:center; padding:10px;">
        <div class="panel-icon pull-left bg-secondary1" style="font-size:30px; width:50px; height:50px; display:flex; align-items:center; justify-content:center; border-radius:5px;">
          <i class="glyphicon glyphicon-user"></i>
        </div>
        <div class="panel-value pull-right" style="text-align:right;">
          <h2 style="margin:0; font-size:20px;"><?php echo $c_user['total']; ?></h2>
          <p class="text-muted" style="margin:0; font-size:14px;">Users</p>
        </div>
      </div>
    </div>
  </a>

  <a href="categorie.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix" style="height:120px; display:flex; align-items:center; padding:10px;">
        <div class="panel-icon pull-left bg-red" style="font-size:30px; width:50px; height:50px; display:flex; align-items:center; justify-content:center; border-radius:5px;">
          <i class="glyphicon glyphicon-th-large"></i>
        </div>
        <div class="panel-value pull-right" style="text-align:right;">
          <h2 style="margin:0; font-size:20px;"><?php echo $c_categorie['total']; ?></h2>
          <p class="text-muted" style="margin:0; font-size:14px;">Categories</p>
        </div>
      </div>
    </div>
  </a>

  <a href="product.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix" style="height:120px; display:flex; align-items:center; padding:10px;">
        <div class="panel-icon pull-left bg-blue2" style="font-size:30px; width:50px; height:50px; display:flex; align-items:center; justify-content:center; border-radius:5px;">
          <i class="glyphicon glyphicon-shopping-cart"></i>
        </div>
        <div class="panel-value pull-right" style="text-align:right;">
          <h2 style="margin:0; font-size:20px;"><?php echo $c_product['total']; ?></h2>
          <p class="text-muted" style="margin:0; font-size:14px;">Products</p>
        </div>
      </div>
    </div>
  </a>

  <a href="sales.php" style="color:black;">
    <div class="col-md-3">
      <div class="panel panel-box clearfix" style="height:120px; display:flex; align-items:center; padding:10px;">
        <div class="panel-icon pull-left bg-green" style="font-size:30px; width:50px; height:50px; display:flex; align-items:center; justify-content:center; border-radius:5px;">
          <i class="glyphicon glyphicon-usd"></i>
        </div>
        <div class="panel-value pull-right" style="text-align:right;">
          <h2 style="margin:0; font-size:20px;"><?php echo $c_sale['total']; ?></h2>
          <p class="text-muted" style="margin:0; font-size:14px;">Sales</p>
        </div>
      </div>
    </div>
  </a>
</div>

<!-- Charts Section -->
<div class="row mt-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><strong>Summary Overview</strong></div>
      <div class="card-body">
        <div id="summaryChart"></div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><strong>Highest Selling Products</strong></div>
      <div class="card-body">
        <div id="productsChart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header"><strong>Recent Sales Trend</strong></div>
      <div class="card-body">
        <div id="salesChart"></div>
      </div>
    </div>
  </div>
</div>

<!-- Existing Tables Section -->
<div class="row mt-4">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>Highest Selling Products</strong></div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th>Title</th>
              <th>Total Sold</th>
              <th>Total Quantity</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as $product_sold): ?>
            <tr>
              <td><?php echo remove_junk(first_character($product_sold['name'])); ?></td>
              <td><?php echo (int)$product_sold['totalSold']; ?></td>
              <td><?php echo (int)$product_sold['totalQty']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>Latest Sales</strong></div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Product Name</th>
              <th>Date</th>
              <th>Total Sale</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_sales as $recent_sale): ?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td>
                <a href="edit_sale.php?id=<?php echo (int)$recent_sale['id']; ?>">
                  <?php echo remove_junk(first_character($recent_sale['name'])); ?>
                </a>
              </td>
              <td><?php echo remove_junk(ucfirst($recent_sale['date'])); ?></td>
              <td>$<?php echo remove_junk(first_character($recent_sale['price'])); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>Recently Added Products</strong></div>
      <div class="panel-body">
        <div class="list-group">
          <?php foreach ($recent_products as $recent_product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?php echo (int)$recent_product['id'];?>">
              <h4 class="list-group-item-heading">
                <?php if($recent_product['media_id'] === '0'): ?>
                  <img class="img-avatar img-circle" src="uploads/products/no_image.png" alt="">
                <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $recent_product['image'];?>" alt="" />
                <?php endif;?>
                <?php echo remove_junk(first_character($recent_product['name']));?>
                <span class="label label-warning pull-right">
                  $<?php echo (int)$recent_product['sale_price']; ?>
                </span>
              </h4>
              <span class="list-group-item-text pull-right">
                <?php echo remove_junk(first_character($recent_product['categorie'])); ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ApexCharts and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
  // 1. Summary Bar Chart
  var optionsSummary = {
    chart: { type: 'bar', height: 350 },
    series: [{ name: 'Total', data: [
      <?php echo $c_user['total']; ?>,
      <?php echo $c_categorie['total']; ?>,
      <?php echo $c_product['total']; ?>,
      <?php echo $c_sale['total']; ?>
    ]}],
    xaxis: { categories: ['Users', 'Categories', 'Products', 'Sales'] },
    colors: ['#6c757d', '#dc3545', '#007bff', '#28a745']
  };
  new ApexCharts(document.querySelector("#summaryChart"), optionsSummary).render();

  // 2. Highest Selling Products Pie Chart
  var productNames = [
    <?php foreach ($products_sold as $p) { echo "'".remove_junk(first_character($p['name']))."',"; } ?>
  ];
  var productTotals = [
    <?php foreach ($products_sold as $p) { echo (int)$p['totalSold'].','; } ?>
  ];
  var optionsProducts = {
    chart: { type: 'pie', height: 350 },
    series: productTotals,
    labels: productNames,
    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
    legend: { position: 'bottom' }
  };
  new ApexCharts(document.querySelector("#productsChart"), optionsProducts).render();

  // 3. Recent Sales Trend Line Chart
  var salesDates = [
    <?php foreach ($recent_sales as $s) { echo "'".remove_junk(ucfirst($s['date']))."',"; } ?>
  ];
  var salesValues = [
    <?php foreach ($recent_sales as $s) { echo (int)$s['price'].','; } ?>
  ];
  var optionsSales = {
    chart: { type: 'line', height: 350 },
    series: [{ name: 'Sale Amount', data: salesValues }],
    xaxis: { categories: salesDates },
    stroke: { curve: 'smooth' },
    markers: { size: 5 },
    colors: ['#007bff']
  };
  new ApexCharts(document.querySelector("#salesChart"), optionsSales).render();
});
</script>

<?php include_once('layouts/footer.php'); ?>
