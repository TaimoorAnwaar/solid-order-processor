<?php

interface OrderValidatorInterface {
    public function validate(array $orderData): void;
}

class OrderValidator implements OrderValidatorInterface {
    public function validate(array $orderData): void {
        if (empty($orderData['product']) || empty($orderData['quantity'])) {
            throw new Exception("Invalid order data");
        }
    }
}

interface OrderRepositoryInterface {
    public function save(array $orderData): void;
}

class MySQLOrderRepository implements OrderRepositoryInterface {
    public function save(array $orderData): void {
        $conn = new mysqli("localhost", "root", "", "orders_db");
        $stmt = $conn->prepare("INSERT INTO orders (product, quantity) VALUES (?, ?)");
        $stmt->bind_param("si", $orderData['product'], $orderData['quantity']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

interface MailerInterface {
    public function send(string $to, string $subject, string $message): void;
}

class SimpleMailer implements MailerInterface {
    public function send(string $to, string $subject, string $message): void {
        mail($to, $subject, $message);
    }
}

class OrderProcessor {
    private $validator;
    private $repository;
    private $mailer;

    public function __construct(
        OrderValidatorInterface $validator,
        OrderRepositoryInterface $repository,
        MailerInterface $mailer
    ) {
        $this->validator = $validator;
        $this->repository = $repository;
        $this->mailer = $mailer;
    }

    public function processOrder(array $orderData): void {
        $this->validator->validate($orderData);
        $this->repository->save($orderData);
        $this->mailer->send(
            $orderData['email'],
            "Order Confirmation",
            "Thank you for ordering " . $orderData['product']
        );
        echo "Order processed successfully!";
    }
}

// Usage example:
$validator = new OrderValidator();
$repository = new MySQLOrderRepository();
$mailer = new SimpleMailer();
$orderProcessor = new OrderProcessor($validator, $repository, $mailer);

$orderData = [
    'product' => 'Book',
    'quantity' => 2,
    'email' => 'customer@example.com'
];

$orderProcessor->processOrder($orderData); 