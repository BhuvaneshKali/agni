<?php
// Connect to the database
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "pharmacy";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Get the details for the invoice
$invoice_number = $_POST['invoice_number'];
$customer_name = $_POST['customer_name'];
$customer_email = $_POST['customer_email'];
$invoice_date = $_POST['invoice_date'];

// Get the items for the invoice
$items = $_POST['items'];

// Calculate the total cost
$total_cost = 0;
foreach ($items as $item) {
  $query = "SELECT price FROM products WHERE id = $item['product_id']";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  $total_cost += $row['price'] * $item['quantity'];
}

// Insert the invoice into the database
$query = "INSERT INTO invoices (invoice_number, customer_name, customer_email, invoice_date, total_cost) VALUES ('$invoice_number', '$customer_name', '$customer_email', '$invoice_date', '$total_cost')";
mysqli_query($conn, $query);

// Generate the invoice HTML
$invoice_html = "
<html>
<head>
  <style>
    /* Styles for the invoice */
  </style>
</head>
<body>
  <h1>Invoice #$invoice_number</h1>
  <p>Customer: $customer_name ($customer_email)</p>
  <p>Invoice date: $invoice_date</p>
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
";

foreach ($items as $item) {
  $product_id = $item['product_id'];
  $quantity = $item['quantity'];

  $query = "SELECT name, price FROM products WHERE id = $product_id";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  $product_name = $row['name'];
  $product_price = $row['price'];
  $item_total = $product_price * $quantity;

  $invoice_html .= "
      <tr>
        <td>$product_name</td>
        <td>$quantity</td>
        <td>$product_price</td>
        <td>$item_total</td>
      </tr>
  ";
}

$invoice_html .= "
    </tbody>
    <tfoot>
      <tr>
        <td colspan='3'>Total:</td>
        <td>$total_cost</td>
      </tr>
    </tfoot>
  </table>
</body>
</html>
";

// Output the invoice HTML
echo $invoice_html;

// Close the database connection
mysqli_close($conn);
?>
