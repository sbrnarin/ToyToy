<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM orders WHERE id = $id");
    $order = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $order_date = $_POST['order_date'];
    $customer_name = $_POST['customer_name'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];

    $conn->query("UPDATE orders SET 
        product_id = '$product_id',
        order_date = '$order_date',
        customer_name = '$customer_name',
        amount = '$amount',
        status = '$status'
        WHERE id = $id");

    header("Location: admin.php");
    exit;
}
?>

<h2>Edit Order</h2>
<form method="POST">
    <label>Product ID: <input type="text" name="product_id" value="<?= $order['product_id'] ?>"></label><br>
    <label>Order Date: <input type="date" name="order_date" value="<?= $order['order_date'] ?>"></label><br>
    <label>Customer Name: <input type="text" name="customer_name" value="<?= $order['customer_name'] ?>"></label><br>
    <label>Amount: <input type="number" name="amount" value="<?= $order['amount'] ?>"></label><br>
    <label>Status:
        <select name="status">
            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="canceled" <?= $order['status'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
        </select>
    </label><br><br>
    <button type="submit">Update Order</button>
</form>
