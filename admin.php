<?php
include "database.php";
// Ambil total jumlah order
$total_orders_query = "SELECT COUNT(*) as total FROM orders";
$total_orders_result = $conn->query($total_orders_query);
$total_orders = $total_orders_result->fetch_assoc()['total'];

// Ambil data order
$sql = "SELECT * FROM orders ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clean - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-background {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
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
                    <span class="text-xl font-bold text-gray-800">E-Clean Admin</span>
                </a>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Total Orders: <?php echo $total_orders; ?></span>
                    <a href="logout.php" class="text-red-600 hover:text-red-800 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="gradient-background text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl font-bold mb-4">Order Management</h1>
            <p class="text-xl opacity-90">View and manage all cleaning service orders</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Recent Orders</h2>
            </div>
            
            <!-- Orders Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if ($result->num_rows > 0) {
                            $counter = $total_orders;
                            while($row = $result->fetch_assoc()) {
                                $animationDelay = (($total_orders - $counter + 1) * 0.1) . 's';
                        ?>
                        <tr class="hover:bg-gray-50 animate-fade-in" style="animation-delay: <?php echo $animationDelay; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $counter; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['cleaning_type']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="max-w-xs truncate" title="<?php echo htmlspecialchars($row['address']); ?>">
                                    <?php echo htmlspecialchars($row['address']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['date']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">Rp <?php echo number_format($row['total_cost'], 0, ',', '.'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php 
                                $paymentMethod = strtoupper(htmlspecialchars($row['pay_method']));
                                echo $paymentMethod === 'CASH' 
                                    ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Cash</span>' 
                                    : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">' . $paymentMethod . '</span>'; 
                                ?>
                            </td>
                            
                        </tr>
                        <?php 
                            $counter--;
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center py-4 text-gray-500'>No orders found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal for Order Details (Optional) -->
        
    </main>

   

    <script>
    function viewOrderDetails(orderId) {
        const modal = document.getElementById('orderDetailsModal');
        const content = document.getElementById('orderDetailsContent');
        
        // Simulate fetching order details (in real app, use AJAX)
        content.innerHTML = `
            <p class="text-gray-600 mb-4">Fetching details for Order ID: ${orderId}</p>
            <button onclick="closeModal()" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Close</button>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('orderDetailsModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function deleteOrder(orderId) {
        if(confirm('Are you sure you want to delete this order?')) {
            // Implement AJAX call or redirect to delete script
            window.location.href = 'delete_order.php?id=' + orderId;
        }
    }
    </script>
</body>
</html>

<?php
$conn->close();
?>