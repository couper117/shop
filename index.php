<?php
require 'config.php';

// Fetch products
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$sql = "SELECT * FROM products";
if ($typeFilter === 'day' || $typeFilter === 'night') {
    $sql .= " WHERE type = '".$conn->real_escape_string($typeFilter)."'";
}
$products = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Newtextile Social Company Limited</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <div class="navbar">
    <div class="brand">Newtextile Social Company Limited</div>
    <div class="nav-toggle" onclick="toggleNav()">
      <span></span><span></span><span></span>
    </div>
    <div class="nav-links" id="navLinks">
      <a href="index.php">Home</a>
      <a href="index.php?type=day">Day Curtains</a>
      <a href="index.php?type=night">Night Curtains</a>
      <a href="#contact">Contact</a>
      <a href="admin_login.php">Admin Login</a>
    </div>
  </div>
</header>

<section class="hero">
  <h1>Beautiful Day & Night Curtains</h1>
  <p>Order made‑to‑measure curtains and accessories in meters, with delivery or pickup at our shop.</p>
  <button onclick="document.getElementById('product-section').scrollIntoView({behavior:'smooth'});">
    Shop Now
  </button>
</section>

<div class="container" id="product-section">
  <h2 style="margin-bottom:10px;">
    <?php
    if ($typeFilter === 'day') echo "Day Curtains";
    elseif ($typeFilter === 'night') echo "Night Curtains";
    else echo "All Curtains & Accessories";
    ?>
  </h2>
  <div class="product-grid">
    <?php while($row = $products->fetch_assoc()): ?>
      <div class="product-card">
        <?php if ($row['image']): ?>
          <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="">
        <?php else: ?>
          <img src="assets/placeholder.jpg" alt="">
        <?php endif; ?>
        <div class="product-info">
          <h3><?php echo htmlspecialchars($row['name']); ?></h3>
          <p>Type: <?php echo htmlspecialchars($row['type']); ?> curtain</p>
          <p class="price"><?php echo number_format($row['price_per_meter'],2); ?> RWF per meter</p>
          <p><?php echo nl2br(htmlspecialchars(substr($row['description'],0,80))); ?>...</p>
          <button
            onclick="openOrderModal(
              <?php echo $row['id']; ?>,
              '<?php echo htmlspecialchars(addslashes($row['name'])); ?>',
              '<?php echo $row['type']; ?>',
              '<?php echo $row['price_per_meter']; ?>'
            )">
            Order now
          </button>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<div class="container" id="contact" style="margin-bottom:30px;">
  <h2>Contact & Shop</h2>
  <p>Phone/WhatsApp: <strong>+250700000000</strong> (replace with your real company number).</p>
  <p>Address: Kigali, Rwanda.</p>
</div>

<footer>
  &copy; <?php echo date('Y'); ?> Newtextile Social Company Limited. All rights reserved.
</footer>

<!-- Order Modal -->
<div class="modal" id="orderModal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeOrderModal()">&times;</span>
    <h2>Order Curtains</h2>
    <form action="place_order.php" method="post">
      <input type="hidden" name="product_id" id="product_id">
      <input type="hidden" name="product_type" id="product_type">
      <input type="hidden" name="price_per_meter" id="price_per_meter">
      <p id="product_summary" style="font-size:0.9rem;margin-bottom:10px;"></p>

      <label>Your name</label>
      <input type="text" name="customer_name" required>

      <label>Phone (WhatsApp)</label>
      <input type="text" name="phone" required>

      <label>Address</label>
      <textarea name="address" rows="2" required></textarea>

      <label>Meters required</label>
      <input type="number" name="meters" step="0.1" min="0.5" required>

      <label>Delivery method</label>
      <select name="delivery_method" required>
        <option value="delivery">Deliver to my address</option>
        <option value="pickup">I will come to the shop</option>
      </select>

      <button type="submit">Confirm Order & Send to WhatsApp</button>
    </form>
  </div>
</div>

<script>
function toggleNav() {
  const nav = document.getElementById('navLinks');
  nav.classList.toggle('show');
}

function openOrderModal(id, name, type, price) {
  document.getElementById('product_id').value = id;
  document.getElementById('product_type').value = type;
  document.getElementById('price_per_meter').value = price;
  document.getElementById('product_summary').innerText =
    name + " (" + type + " curtain) - " + parseFloat(price).toFixed(2) + " RWF per meter";
  document.getElementById('orderModal').classList.add('show');
}

function closeOrderModal() {
  document.getElementById('orderModal').classList.remove('show');
}
</script>
</body>
</html>
