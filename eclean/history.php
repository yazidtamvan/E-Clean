<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "regisnlogin";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengambil data dari database
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

$orders = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$jsonOrders = json_encode($orders);
// Mengubah data menjadi JSON
// echo json_encode($orders);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Dashboard User</h1>
        <a href="index.html" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
            Logout
        </a>
    </header>

    <!-- Main Content -->
    <main class="py-8">
        <div class="container mx-auto max-w-screen-lg px-4">
            <!-- Order History Section -->
            <section class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Riwayat Pesanan Anda</h2>
                <div id="orderHistoryList" class="space-y-4">
                    <!-- Order items will be injected here by JavaScript -->
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 text-sm">
        <p>&copy; 2024 E-Clean. All rights reserved.</p>
        <div class="mt-2">
            <a href="#" class="text-gray-400 hover:text-white mx-2">Privacy Policy</a>
            <a href="#" class="text-gray-400 hover:text-white mx-2">Terms of Service</a>
        </div>
    </footer>

    <script>
        localStorage.setItem('orderHistory', JSON.stringify(<?php echo $jsonOrders; ?>));
        // Get order history from localStorage
        let orderHistory = JSON.parse(localStorage.getItem('orderHistory')) || [];
        // console.log(orderHistory);
        // Display order history on the page
        function displayOrderHistory() {
            const orderHistoryList = document.getElementById("orderHistoryList");
            orderHistoryList.innerHTML = "";

            if (orderHistory.length === 0) {
                orderHistoryList.innerHTML = "<p class='text-gray-500'>No orders found.</p>";
                return;
            }
            orderHistory.sort((a, b) => b.id - a.id);
            orderHistory.forEach(order => {
                const orderElement = document.createElement("div");
                orderElement.className = "bg-gray-50 p-4 rounded-lg shadow-md";
                orderElement.innerHTML = `
                    <p><strong class="text-gray-700">Tanggal:</strong> ${order.date}</p>
                    <p><strong class="text-gray-700">Jasa:</strong> ${order.cleaning_type}</p>
                    <p><strong class="text-gray-700">Durasi:</strong> ${order.duration}</p>
                    <p><strong class="text-gray-700">Metode Pembayaran:</strong> ${order.pay_method}</p>
                    <p><strong class="text-gray-700">Status:</strong> ${order.status}</p>
                `;
                orderHistoryList.appendChild(orderElement);
            });
        }

        // Call function to display order history
        displayOrderHistory();
    </script>
</body>
</html>
