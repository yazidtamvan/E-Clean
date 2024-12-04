<?php
session_start();
include "database.php";

// Initialize variables
$orderDetails = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'date' => '',
    'time' => '',
    'cleaning_type' => '',
    'duration' => '',
    'total_cost' => '',
    'payment_method' => '',
    'payment_number' => ''
];

// Check if there are query parameters
if (isset($_GET['id'])) {
    // Populate the variables with the query parameters
    $orderDetails['name'] = $_GET['name'] ?? '';
    $orderDetails['email'] = $_GET['email'] ?? '';
    $orderDetails['phone'] = $_GET['phone'] ?? '';
    $orderDetails['address'] = $_GET['address'] ?? '';
    $orderDetails['date'] = $_GET['date'] ?? '';
    $orderDetails['time'] = $_GET['time'] ?? '';
    $orderDetails['cleaning_type'] = $_GET['cleaning_type'] ?? '';
    $orderDetails['duration'] = $_GET['duration'] ?? '';
    $orderDetails['total_cost'] = $_GET['total_cost'] ?? '';
    $orderDetails['payment_method'] = $_GET['payment_method'] ?? '';
    $orderDetails['payment_number'] = $_GET['payment_number'] ?? '';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $preferred_date = mysqli_real_escape_string($conn, $_POST['date']);
    $preferred_time = mysqli_real_escape_string($conn, $_POST['time']);
    $cleaning_type = mysqli_real_escape_string($conn, $_POST['cleaning_type']);
    $duration = intval($_POST['duration']);
    $total_cost = floatval($_POST['total_cost']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_number = isset($_POST['payment_number']) ? mysqli_real_escape_string($conn, $_POST['payment_number']) : null;

    // Insert new order into the database
    $sql = "INSERT INTO orders (name, email, phone, address, date, time, cleaning_type, duration, total_cost, pay_method, pay_num, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssidssi", $name, $email, $phone, $address, $preferred_date, $preferred_time, 
                      $cleaning_type, $duration, $total_cost, $payment_method, $payment_number, $_SESSION['user_id']);
    
                      if ($stmt->execute()) {
                        // Order created successfully, now delete the old order
                        $oldOrderId = intval($_GET['id']);
                        $deleteSql = "DELETE FROM orders WHERE id = ? AND user_id = ?";
                        $deleteStmt = $conn->prepare($deleteSql);
                        $deleteStmt->bind_param("ii", $oldOrderId, $_SESSION['user_id']);
                        $deleteStmt->execute();
                        $deleteStmt->close();
                    
                        echo "<script>
                            alert('Order updated successfully! Old order deleted.');
                            window.location.href = 'history.php'; // Redirect to the order history page
                        </script>";
                    } else {
                        echo "<p class='text-red-500 text-center'>Error: " . $stmt->error . "</p>";
                    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clean - Order Cleaning Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .form-input:focus {
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        
        .service-card {
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .service-card.selected {
            border: 2px solid #2563eb;
            background-color: #eff6ff;
        }

        .gradient-background {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-100">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a class="flex items-center space-x-3" href="#">
                    <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.437 17.328a9.97 9.97 0 0 0 1.563-5.328c0-5.523-4.477-10-10-10S2 6.477 2 12s4.477 10 10 10a9.967 9.967 0 0 0 6.192-2.166c.577.737 1.444 1.166 2.363 1.166a3 3 0 0 0 3-3c0-1.038-.67-1.924-1.616-2.272zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-800">E-Clean</span>
                </a>
                <a href="dashboard_user.php" class="flex items-center space-x-2 text-gray-600 hover:text-blue-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="gradient-background text-white py-16 mb-12">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold mb-4 animate-slide-in">Book Your Cleaning Service</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto animate-slide-in" style="animation-delay: 0.2s">
                Experience professional cleaning services tailored to your needs
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 pb-16">
        <form method="POST" action="" class="max-w-4xl mx-auto">
            <!-- Service Selection Cards -->
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                <div id="residential-card" class="service-card bg-white rounded-xl shadow-md p-6 border border-gray-100 cursor-pointer" onclick="selectService('Residential Cleaning', 30000, 'residential-card')">
                    <div class="text-blue-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <input type="hidden" id="cleaning_type" name="cleaning_type" value="Residential Cleaning">
                    <p class="text-gray-600 mb-4">Perfect for homes and apartments</p>
                    <p class="text-blue-600 font-semibold">Rp 30,000/hour</p>
                </div>

                <div id="eco-friendly-card" class="service-card bg-white rounded-xl shadow-md p-6 border border-gray-100 cursor-pointer" onclick="selectService('Eco Friendly Cleaning', 40000, 'eco-friendly-card')">
                    <div class="text-blue-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <input type="hidden" id="cleaning_type" name="cleaning_type" value="Commercial Cleaning">
                    <p class="text-gray-600 mb-4">Ideal for offices and business spaces</p>
                    <p class="text-blue-600 font-semibold">Rp 40,000/hour</p>
                </div>

                <div id="commercial-card" class="service-card bg-white rounded-xl shadow-md p-6 border border-gray-100 cursor-pointer" onclick="selectService('Commercial Cleaning', 50000, 'commercial-card')">
                    <div class="text-blue-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7v2a1 1 0 001 1h1m0 0h1a1 1 0 011 1v10a1 1 0 01-1 1H5a1 1 0 01-1-1V11a1 1 0 011-1h1m0 0h1a1 1 0 001-1V7m0 10h8m-4 0v4m-5-4h14"></path>
                        </svg>
                    </div>
                    <input type="hidden" id="cleaning_type" name="cleaning_type" value="Eco- Friendly Cleaning">
                    <p class="text-gray-600 mb-4">Eco-conscious cleaning for your space</p>
                    <p class="text-blue-600 font-semibold">Rp 50,000/hour</p>
                </div>
            </div>

            <!-- Form for User Details -->
            <form method="POST" action="">
    <div class="mb-6">
        <label for="name" class="block text-lg font-semibold mb-2">Full Name:</label>
        <input type="text" id="name" name="name" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" >
    </div>

    <div class="mb-6">
        <label for="email" class="block text-lg font-semibold mb-2">Email:</label>
        <input type="email" id="email" name="email" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" >
    </div>

    <div class="mb-6">
        <label for="phone" class="block text-lg font-semibold mb-2">Phone Number:</label>
        <input type="text" id="phone" name="phone" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" value="<?php echo isset($user) ? htmlspecialchars($user['phone']) : ''; ?>" required>
    </div>

    <div class="mb-6">
        <label for="address" class="block text-lg font-semibold mb-2">Address:</label>
        <textarea id="address" name="address" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" rows="3" required><?php echo isset($user) ? htmlspecialchars($user['address']) : ''; ?></textarea>
    </div>

    

            <!-- Duration and Cost Calculation -->
            <div class="mb-6">
                <label for="duration" class="block text-lg font-semibold mb-2">Duration (hours):</label>
                <input type="number" id="duration" name="duration" min="1" value="1" onchange="updateTotalCost()" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" required>
            </div>

            <div class="mb-6">
                <label for="total_cost" class="block text-lg font-semibold mb-2">Total Cost (Rp):</label>
                <input type="text" id="total_cost" name="total_cost" value="30000" readonly class="form-input bg-gray-200 border border-gray-300 rounded-lg p-3 w-full" required>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <label for="payment_method" class="block text-lg font-semibold mb-2">Payment Method:</label>
                <select id="payment_method" name="payment_method" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" onchange="togglePaymentNumberField()" required>
                    <option value="Cash">Cash</option>
                    <option value="GoPay">GoPay</option>
                    <option value="Dana">Dana</option>
                </select>
            </div>

            <!-- Payment Number -->
            <div class="mb-6" id="payment_number_field" style="display: none;">
                <label for="payment_number" class="block text-lg font-semibold mb-2">Payment Number:</label>
                <input type="text" id="payment_number" name="payment_number" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full">
            </div>

            <!-- Date and Time -->
            <div class="mb-6">
                <label for="date" class="block text-lg font-semibold mb-2">Service Date:</label>
                <input type="date" id="date" name="date" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" required>
            </div>

            <div class="mb-6">
                <label for="time" class="block text-lg font-semibold mb-2">Service Time:</label>
                <input type="time" id="time" name="time" class="form-input bg-white border border-gray-300 rounded-lg p-3 w-full" required>
            </div>

            <!-- Submit Button -->
            <div class="mb-6">
                <button type="submit" class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg w-full">
                    Confirm Booking
                </button>
            </div>
        </form>
    </main>

    <script>
        let selectedService = null;
        let selectedServicePrice = 30000; // Default price (Residential Cleaning)

        function selectService(serviceName, price, cardId) {
            // Deselect previous service
            if (selectedService) {
                document.getElementById(selectedService).classList.remove("selected");
            }

            // Select new service
            selectedService = cardId;
            selectedServicePrice = price;
            document.getElementById(cardId).classList.add("selected");

            // Update cleaning type and total cost
            document.getElementById("cleaning_type").value = serviceName;
            updateTotalCost();
        }

        function updateTotalCost() {
            const duration = document.getElementById("duration").value;
            const totalCost = selectedServicePrice * duration;
            document.getElementById("total_cost").value = totalCost;
        }

        function togglePaymentNumberField() {
            const paymentMethod = document.getElementById("payment_method").value;
            const paymentNumberField = document.getElementById("payment_number_field");

            if (paymentMethod === "Cash") {
                paymentNumberField.style.display = "none";
            } else {
                paymentNumberField.style.display = "block";
            }
        }
        function fetchOrderHistory() {
    fetch('dashboard_user.php?action=fetchOrders')
        .then(response => response.json())
        .then(data => {
            const orderHistoryContainer = document.getElementById('orderHistory');
            orderHistoryContainer.innerHTML = ''; // Clear existing content

            // Initialize stats counters
            let totalOrders = 0;
            let activeOrders = 0;
            let completedOrders = 0;

            if (data.length > 0) {
                data.forEach(order => {
                    // Update stats
                    totalOrders++;
                    if (order.status.toLowerCase() === 'completed') {
                        completedOrders++;
                    } else {
                        activeOrders++;
                    }

                    const orderCard = document.createElement('div');
                    orderCard.classList.add('order-card', 'bg-white', 'p-6', 'rounded-lg', 'shadow-lg', 'mb-4');
                    orderCard.innerHTML = `
                        <h3 class="font-semibold text-lg">${order.cleaning_type} Cleaning</h3>
                        <p><strong>Order ID:</strong> ${order.id}</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span></p>
                        <p><strong>Date:</strong> ${order.date}</p>
                        <p><strong>Time:</strong> ${order.time}</p>
                        <p><strong>Total:</strong> ${order.total_cost}</p>
                    `;
                    orderHistoryContainer.appendChild(orderCard);
                });
            } else {
                orderHistoryContainer.innerHTML = '<p class="text-center text-gray-500">No orders found</p>';
            }

            // Update stats display
            document.getElementById('totalOrders').textContent = totalOrders;
            document.getElementById('activeOrders').textContent = activeOrders;
            document.getElementById('completedOrders').textContent = completedOrders;
        })
        .catch(error => console.error('Error fetching order history:', error));
}

    </script>
</body>
</html>