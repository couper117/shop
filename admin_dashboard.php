<?php
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle new product
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name  = $conn->real_escape_string($_POST['name']);
    $type  = $conn->real_escape_string($_POST['type']);
    $price = floatval($_POST['price_per_meter']);
    $desc  = $conn->real_escape_string($_POST['description']);

    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time().'_'.rand(1000,9999).'.'.$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir.$imageName);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, type, price_per_meter, image, description)
                            VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssdss", $name, $type, $price, $imageName, $desc);
    $stmt->execute();
    $stmt->close();
    $msg = "Product added successfully.";
}

// Fetch products and latest orders
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
$orders   = $conn->query("SELECT o.*, p.name AS product_name
                          FROM orders o
                          JOIN products p ON p.id = o.product_id
                          ORDER BY o.id DESC
                          LIMIT 20");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Newtextile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <div class="navbar">
    <div class="brand">Newtextile Admin</div>
    <div class="nav-links">
      <a href="index.php">View Shop</a>
      <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a>
    </div>
  </div>
</header>

<div class="admin-container">
  <h2>Add Product</h2>
  <?php if ($msg): ?>
    <p style="color:green;"><?php echo $msg; ?></p>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="add_product" value="1">

    <label>Product name</label>
    <input type="text" name="name" required>

    <label>Type</label>
    <select name="type" required>
      <option value="day">Day curtain</option>
      <option value="night">Night curtain</option>
    </select>

    <label>Price per meter (RWF)</label>
    <input type="number" step="0.01" name="price_per_meter" required>

    <label>Image</label>
    <input type="file" name="image" accept="image/*">

    <label>Description</label>
    <textarea name="description" rows="3"></textarea>

    <button type="submit">Save Product</button>
  </form>

  <hr style="margin:25px 0;">

  <h2>Products</h2>
  <table>
    <tr>
      <th>ID</th><th>Name</th><th>Type</th><th>Price/m</th><th>Image</th>
    </tr>
    <?php while($p = $products->fetch_assoc()): ?>
      <tr>
        <td><?php echo $p['id']; ?></td>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td><?php echo htmlspecialchars($p['type']); ?></td>
        <td><?php echo number_format($p['price_per_meter'],2); ?></td>
        <td>
          <?php if ($p['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($p['image']); ?>" style="height:40px;">
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <hr style="margin:25px 0;">

  <h2>Latest Orders</h2>
  <table>
    <tr>
      <th>ID</th><th>Customer</th><th>Phone</th><th>Product</th><th>Type</th><th>Meters</th><th>Delivery</th><th>Date</th>
    </tr>
    <?php while($o = $orders->fetch_assoc()): ?>
      <tr>
        <td><?php echo $o['id']; ?></td>
        <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
        <td><?php echo htmlspecialchars($o['phone']); ?></td>
        <td><?php echo htmlspecialchars($o['product_name']); ?></td>
        <td><?php echo htmlspecialchars($o['curtain_type']); ?></td>
        <td><?php echo $o['meters']; ?></td>
        <td><?php echo htmlspecialchars($o['delivery_method']); ?></td>
        <td><?php echo $o['created_at']; ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
