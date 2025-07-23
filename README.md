<?php

class OrderProcessor
{
    public function processOrder($orderData)
    {
        // Validate the order
        if (empty($orderData['product']) || empty($orderData['quantity'])) {
            throw new Exception("Invalid order data");
        }

        // Save to database (simulated)
        $conn = new mysqli("localhost", "root", "", "orders_db");
        $sql = "INSERT INTO orders (product, quantity) VALUES ('" . $orderData['product'] . "', " . $orderData['quantity'] . ")";
        $conn->query($sql);
        $conn->close();

        // Send confirmation email
        mail($orderData['email'], "Order Confirmation", "Thank you for ordering " . $orderData['product']);

        echo "Order processed successfully!";
    }
}


