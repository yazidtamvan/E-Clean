<?php
session_start();
include "database.php";

// Fungsi untuk mengambil data order dari database
function getOrderHistory($conn) {
    $sql = "SELECT * FROM orders where user_id = " . $_SESSION['user_id'] . " ORDER BY date DESC";
    $result = $conn->query($sql);
    $orders = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $orders[] = array(
                'id' => $row['id'],
                'date' => $row['date'],
                'cleaning_type' => $row['cleaning_type'],
                'duration' => $row['duration'],
                'total_cost' => $row['total_cost'],
                'pay_method' => $row['pay_method'],
                'status' => 'Completed' // Default status, can be modified based on your needs
            );
        }
    }
    
    return $orders;
}

// Cek jika form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
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

    // Masukkan data ke database
    $sql = "INSERT INTO orders (name, email, phone, address, date, time, cleaning_type, duration, total_cost, pay_method, pay_num, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssidssi", $name, $email, $phone, $address, $preferred_date, $preferred_time, 
                      $cleaning_type, $duration, $total_cost, $payment_method, $payment_number, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "<script>
            window.location.href = 'dashboard_user.html';
        </script>";
    } else {
        echo "<p class='text-red-500 text-center'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Ambil data order untuk ditampilkan
$orderHistory = getOrderHistory($conn);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clean - Order History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f6f9fc 0%, #eef2f7 100%);
            min-height: 100vh;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-onway { background-color: #fff3cd; color: #856404; }
        .status-process { background-color: #cce5ff; color: #004085; }
        .status-completed { background-color: #d4edda; color: #155724; }

        .order-card {
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .stats-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <!-- Header -->
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

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Orders</p>
                        <h3 class="text-2xl font-bold text-gray-800" id="totalOrders">0</h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Active Orders</p>
                        <h3 class="text-2xl font-bold text-gray-800" id="activeOrders">0</h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Completed Orders</p>
                        <h3 class="text-2xl font-bold text-gray-800" id="completedOrders">0</h3>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History Section -->
        <section class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Order History</h2>
                <p class="text-gray-500 text-sm mt-1">Track all your cleaning service orders</p>
            </div>
            <div id="orderHistoryList" class="divide-y divide-gray-200">
                <!-- Orders will be injected here -->
            </div>
        </section>
    </main>


    <script>
        // Initialize order history from PHP data
        let orderHistory = <?php echo json_encode($orderHistory); ?>;

        function updateOrderStatus(order) {
            const now = new Date();
            const orderDate = new Date(order.date);
            const durationInMilliseconds = order.duration * 60 * 60 * 1000;
            const endTime = new Date(orderDate.getTime() + durationInMilliseconds);

            if (now < orderDate) {
                return "On the Way";
            } else if (now >= orderDate && now <= endTime) {
                return "On Process";
            } else {
                return "Completed";
            }
        }

        function getStatusBadgeClass(status) {
            switch(status) {
                case 'On the Way':
                    return 'status-onway';
                case 'On Process':
                    return 'status-process';
                case 'Completed':
                    return 'status-completed';
                default:
                    return '';
            }
        }

        function formatCurrency(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
        }

        function updateStats() {
            const totalOrders = orderHistory.length;
            const activeOrders = orderHistory.filter(order => 
                updateOrderStatus(order) === "On the Way" || 
                updateOrderStatus(order) === "On Process"
            ).length;
            const completedOrders = orderHistory.filter(order => 
                updateOrderStatus(order) === "Completed"
            ).length;

            document.getElementById('totalOrders').textContent = totalOrders;
            document.getElementById('activeOrders').textContent = activeOrders;
            document.getElementById('completedOrders').textContent = completedOrders;
        }

        function displayOrderHistory() {
            const orderHistoryList = document.getElementById("orderHistoryList");
            orderHistoryList.innerHTML = "";

            if (orderHistory.length === 0) {
                orderHistoryList.innerHTML = `
                    <div class="p-8 text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-inbox text-4xl"></i>
                        </div>
                        <p class="text-gray-500">No orders found</p>
                    </div>
                `;
                return;
            }

            orderHistory.forEach(order => {
                const status = updateOrderStatus(order);
                const statusClass = getStatusBadgeClass(status);
                
                const orderElement = document.createElement("div");
                orderElement.className = "order-card p-6 hover:bg-gray-50";
                orderElement.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <span class="status-badge ${statusClass}">${status}</span>
                                <p class="text-gray-500 text-sm">${new Date(order.date).toLocaleDateString()}</p>
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-semibold text-gray-800">${order.cleaning_type || 'Standard Cleaning'}</h3>
                                <p class="text-gray-600 text-sm">Duration: ${order.duration || 0} Hours</p>
                                <p class="text-gray-600 text-sm">Amount: ${formatCurrency(order.total_cost || 0)}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Order #${order.id || 'N/A'}</p>
                        </div>
                    </div>
                `;
                orderHistoryList.appendChild(orderElement);
            });
        }

        // Initialize
        displayOrderHistory();
        updateStats();

        // Update every minute
        setInterval(() => {
            displayOrderHistory();
            updateStats();
        }, 60000);
        function displayOrderHistory() {
    const orderHistoryList = document.getElementById("orderHistoryList");
    orderHistoryList.innerHTML = "";

    if (orderHistory.length === 0) {
        orderHistoryList.innerHTML = `
            <div class="p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-inbox text-4xl"></i>
                </div>
                <p class="text-gray-500">No orders found</p>
            </div>
        `;
        return;
    }

    orderHistory.forEach(order => {
        const status = updateOrderStatus(order);
        const statusClass = getStatusBadgeClass(status);

        const orderElement = document.createElement("div");
        orderElement.className = "order-card p-6 hover:bg-gray-50";
        orderElement.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <span class="status-badge ${statusClass}">${status}</span>
                        <p class="text-gray-500 text-sm">${new Date(order.date).toLocaleDateString()}</p>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-semibold text-gray-800">${order.cleaning_type || 'Standard Cleaning'}</h3>
                        <p class="text-gray-600 text-sm">Duration: ${order.duration || 0} Hours</p>
                        <p class="text-gray-600 text-sm">Amount: ${formatCurrency(order.total_cost || 0)}</p>
                    </div>
                </div>
                <div class="text-right space-y-2">
                 ${status === 'On the Way' ? `
                    <div class="flex space-x-2">
                    <a href="new_order.php?id=${order.id}&name=${encodeURIComponent(order.name)}&email=${encodeURIComponent(order.email)}&phone=${encodeURIComponent(order.phone)}&address=${encodeURIComponent(order.address)}&date=${encodeURIComponent(order.date)}&time=${encodeURIComponent(order.time)}&cleaning_type=${encodeURIComponent(order.cleaning_type)}&duration=${order.duration}&total_cost=${order.total_cost}&payment_method=${encodeURIComponent(order.payment_method)}&payment_number=${encodeURIComponent(order.payment_number)}" class="px-3 py-1 text-sm text-white bg-blue-500 hover:bg-blue-600 rounded">
                      Edit
                    </a>
                    <button onclick="deleteOrder(${order.id})" class="px-3 py-1 text-sm text-white bg-red-500 hover:bg-red-600 rounded">
                      Delete
                    </button>
                    </div>
                 ` : ''}
                </div>
            </div>
        `;
        orderHistoryList.appendChild(orderElement);
    });
}
function editOrder(orderId) {
    const order = orderHistory.find(o => o.id === orderId);
    if (!order) return alert("Order not found!");

    const newDate = prompt("Enter new date (YYYY-MM-DD):", order.date);
    const newTime = prompt("Enter new time (HH:MM):", order.time);
    const newDuration = prompt("Enter new duration (hours):", order.duration);

    if (newDate && newTime && newDuration) {
        fetch(`editOrder.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: orderId,
                date: newDate,
                time: newTime,
                duration: newDuration
            })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert("Order updated successfully!");
                  location.reload();
              } else {
                  alert("Failed to update order: " + data.message);
              }
          });
    }
}

function deleteOrder(orderId) {
    if (confirm("Are you sure you want to delete this order?")) {
        fetch(`delete_order.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: orderId })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert("Order deleted successfully!");
                  location.reload();
              } else {
                  alert("Failed to delete order: " + data.message);
              }
          });
    }
}

    </script>
</body>
</html>
