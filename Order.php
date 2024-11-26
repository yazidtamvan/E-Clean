<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "regisnlogin";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek jika form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $preferred_date = $_POST['date'];
    $preferred_time = $_POST['time'];
    $cleaning_type = $_POST['cleaning_type'];
    $duration = $_POST['duration'];
    $total_cost = $_POST['total_cost'];
    $payment_method = $_POST['payment_method'];
    $payment_number = $_POST['payment_number'] ?? null;

    // Masukkan data ke database
    $sql = "INSERT INTO orders (name, email, phone, address, date, time, cleaning_type, duration, total_cost, pay_method, pay_num)
            VALUES ('$name', '$email', '$phone', '$address', '$preferred_date', '$preferred_time', '$cleaning_type', $duration, $total_cost, '$payment_method', '$payment_number')";
    echo $preferred_date;
    echo $cleaning_type;
    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, jalankan JavaScript untuk memperbarui localStorage dan pindah ke dashboard
        echo "<script>
            let orderHistory = JSON.parse(localStorage.getItem('orderHistory')) || [];
            orderHistory.push({
                date: '$preferred_date',
                service: '$cleaning_type',
                amount: 'Rp ' + parseInt('$total_cost').toLocaleString(),
                paymentMethod: '$payment_method',
                status: 'Completed'
            });
            localStorage.setItem('orderHistory', JSON.stringify(orderHistory));
            window.location.href = 'dashboard_user.html'; // Redirect setelah submit
        </script>";
    } else {
        echo "<p class='text-red-500 text-center'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
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
                <a href="dashboard_user.html" class="flex items-center space-x-2 text-gray-600 hover:text-blue-600 transition">
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
                    <h3 class="text-lg font-semibold mb-2">Residential Cleaning</h3>
                    <p class="text-gray-600 mb-4">Perfect for homes and apartments</p>
                    <p class="text-blue-600 font-semibold">Rp 30,000/hour</p>
                </div>

                <div id="commercial-card" class="service-card bg-white rounded-xl shadow-md p-6 border border-gray-100 cursor-pointer" onclick="selectService('Commercial Cleaning', 40000, 'commercial-card')">
                    <div class="text-blue-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Commercial Cleaning</h3>
                    <p class="text-gray-600 mb-4">Ideal for offices and businesses</p>
                    <p class="text-blue-600 font-semibold">Rp 40,000/hour</p>
                </div>

                <div id="eco-card" class="service-card bg-white rounded-xl shadow-md p-6 border border-gray-100 cursor-pointer" onclick="selectService('Friendly Cleaning', 50000, 'eco-card')">
                    <div class="text-blue-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Eco-Friendly Cleaning</h3>
                    <p class="text-gray-600 mb-4">Using environmentally safe products</p>
                    <p class="text-blue-600 font-semibold">Rp 50,000/hour</p>
                </div>
            </div>

            <!-- Form Sections -->
            <div class="bg-white rounded-xl shadow-lg p-8 space-y-8">
                <!-- Hidden Service Selection -->
                <input type="hidden" id="cleaning_type" name="cleaning_type">
                <input type="hidden" id="rate_per_hour" value="0">
                
                <!-- Personal Information -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800 border-b pb-2">Personal Information</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="name">Full Name</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="text" id="name" name="name" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="email">Email Address</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="email" id="email" name="email" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="phone">Phone Number</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="tel" id="phone" name="phone" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="duration">Duration (hours)</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="number" id="duration" name="duration" min="1" required onchange="calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="address">Address</label>
                        <textarea class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" id="address" name="address" rows="3" required></textarea>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800 border-b pb-2">Schedule</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="date">Preferred Date</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="date" id="date" name="date" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="time">Preferred Time</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="time" id="time" name="time" required>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800 border-b pb-2">Payment Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="paymentMethod">Payment Method</label>
                            <select class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" id="paymentMethod" name="payment_method" required>
                                <option value="" disabled selected>Select payment method</option>
                                <option value="cash">Cash</option>
                                <option value="gopay">GoPay</option>
                                <option value="dana">Dana</option>
                            </select>
                        </div>

                        <div id="gopayDetails" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="gopayNumber">GoPay Number</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="text" id="gopayNumber" name="payment_number">
                        </div>

                        <div id="danaDetails" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="danaNumber">Dana Number</label>
                            <input class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition" type="text" id="danaNumber" name="payment_number">
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6 mt-4">
                            <p class="text-lg font-medium text-gray-700">Total Cost: <span id="totalCost" class="text-blue-600 font-bold">Rp 0</span></p>
                            <input type="hidden" id="total_cost" name="total_cost" value="0">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-4 px-6 rounded-lg font-semibold hover:bg-blue-700 transform hover:scale-[1.02] transition-all duration-200">
                    Complete Booking
                </button>
            </div>
        </form>
    </main>

    <footer class="bg-gray-800 text-white text-center py-4">
        <p>© 2024 E-Clean. All rights reserved.</p>
    </footer>

    <script>
    // Pilih layanan kebersihan
    function selectService(serviceName, ratePerHour, cardId) {
        // Simpan nilai ke input tersembunyi
        document.getElementById('cleaning_type').value = serviceName;
        document.getElementById('rate_per_hour').value = ratePerHour;

        // Sorot kartu yang dipilih
        const cards = document.querySelectorAll('.service-card');
        cards.forEach(card => card.classList.remove('selected'));
        document.getElementById(cardId).classList.add('selected');

        // Perbarui total biaya
        calculateTotal();
    }

    // Menghitung total biaya
    function calculateTotal() {
        const ratePerHour = parseInt(document.getElementById('rate_per_hour').value) || 0;
        const duration = parseInt(document.getElementById('duration').value) || 0;
        const total = ratePerHour * duration;

        // Perbarui tampilan total biaya
        document.getElementById('totalCost').textContent = `Rp ${total.toLocaleString()}`;
        document.getElementById('total_cost').value = total;
    }
</script>

</body>
</html>
