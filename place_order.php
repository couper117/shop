<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name   = $conn->real_escape_string($_POST['customer_name']);
    $phone           = $conn->real_escape_string($_POST['phone']);
    $address         = $conn->real_escape_string($_POST['address']);
    $meters          = floatval($_POST['meters']);
    $delivery_method = $conn->real_escape_string($_POST['delivery_method']);
    $product_id      = intval($_POST['product_id']);

    // Fetch product
    $res = $conn->query("SELECT name, type, price_per_meter FROM products WHERE id = $product_id");
    if (!$res || $res->num_rows === 0) {
        die("Invalid product.");
    }
    $product = $res->fetch_assoc();

    $curtain_type = $product['type'];
    $price_per_meter = floatval($product['price_per_meter']);
    $total = $meters * $price_per_meter;

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, address, curtain_type, product_id, meters, delivery_method)
                            VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param(
        "ssssids",
        $customer_name,
        $phone,
        $address,
        $curtain_type,
        $product_id,
        $meters,
        $delivery_method
    );
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Company WhatsApp number (international format, no + in the link)
    $company_number = "250700000000"; // change to your real number

    $msg  = "New Order #%d%%0A";
    $msg .= "Name: %s%%0A";
    $msg .= "Phone: %s%%0A";
    $msg .= "Address: %s%%0A";
    $msg .= "Curtain: %s (%s)%%0A";
    $msg .= "Meters: %.2f%%0A";
    $msg .= "Total: %.2f RWF%%0A";
    $msg .= "Delivery: %s";

    $text = sprintf(
        $msg,
        $order_id,
        urlencode($customer_name),
        urlencode($phone),
        urlencode($address),
        urlencode($product['name']),
        $curtain_type,
        $meters,
        $total,
        $delivery_method === 'delivery' ? 'Deliver to customer' : 'Pickup at shop'
    );

    $wa_url = "https://wa.me/".$company_number."?text=".$text;

    header("Location: ".$wa_url);
    exit;
}
?>
