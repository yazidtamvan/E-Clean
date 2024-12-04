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
            $orders[] = $row;
        }
    }
    
    return $orders;
}

// Fungsi untuk menghapus pesanan
function deleteOrder($conn, $orderId, $userId) {
    $sql = "DELETE FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $orderId, $userId);
    
    return $stmt->execute();
}

// Fungsi untuk mengupdate pesanan
function updateOrder($conn, $orderId, $userId, $data) {
    $sql = "UPDATE orders SET 
            name = ?, 
            email = ?, 
            phone = ?, 
            address = ?, 
            date = ?, 
            time = ?, 
            cleaning_type = ?, 
            duration = ?, 
            total_cost = ?, 
            pay_method = ?, 
            pay_num = ?
            WHERE id = ? AND user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssidssii", 
        $data['name'], 
        $data['email'], 
        $data['phone'], 
        $data['address'], 
        $data['date'], 
        $data['time'], 
        $data['cleaning_type'], 
        $data['duration'], 
        $data['total_cost'], 
        $data['payment_method'], 
        $data['payment_number'],
        $orderId,
        $userId
    );
    
    return $stmt->execute();
}

// Handle order delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_order') {
    $orderId = intval($_POST['order_id']);
    
    if (deleteOrder($conn, $orderId, $_SESSION['user_id'])) {
        echo json_encode(['success' => true, 'message' => 'Order successfully deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete order']);
    }
    exit;
}

// Handle order update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_order') {
    $orderId = intval($_POST['order_id']);
    
    $updateData = [
        'name' => mysqli_real_escape_string($conn, $_POST['name']),
        'email' => mysqli_real_escape_string($conn, $_POST['email']),
        'phone' => mysqli_real_escape_string($conn, $_POST['phone']),
        'address' => mysqli_real_escape_string($conn, $_POST['address']),
        'date' => mysqli_real_escape_string($conn, $_POST['date']),
        'time' => mysqli_real_escape_string($conn, $_POST['time']),
        'cleaning_type' => mysqli_real_escape_string($conn, $_POST['cleaning_type']),
        'duration' => floatval($_POST['duration']),
        'total_cost' => floatval($_POST['total_cost']),
        'payment_method' => mysqli_real_escape_string($conn, $_POST['payment_method']),
        'payment_number' => isset($_POST['payment_number']) ? mysqli_real_escape_string($conn, $_POST['payment_number']) : null
    ];
    
    if (updateOrder($conn, $orderId, $_SESSION['user_id'], $updateData)) {
        echo json_encode(['success' => true, 'message' => 'Order successfully updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order']);
    }
    exit;
}

// Ambil data order untuk ditampilkan
$orderHistory = getOrderHistory($conn);

$conn->close();
?>
<!-- Rest of the existing HTML remains the same, with added modal and script -->

<!-- Add this modal to the existing HTML body, just before the footer -->
<div id="editOrderModal" class="fixed z-50 inset-0 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-height-100vh pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>
        <div class="inline-block align-center bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editOrderForm">
                <div class="bg-white px-6 pt-5 pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Order</h3>
                    <input type="hidden" id="editOrderId" name="order_id">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="editName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="editEmail" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" name="phone" id="editPhone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="address" id="editAddress" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="editDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="time" id="editTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cleaning Type</label>
                            <select name="cleaning_type" id="editCleaningType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                                <option value="Standard Cleaning">Standard Cleaning</option>
                                <option value="Deep Cleaning">Deep Cleaning</option>
                                <option value="Office Cleaning">Office Cleaning</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Duration (Hours)</label>
                            <input type="number" name="duration" id="editDuration" step="0.5" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select name="payment_method" id="editPaymentMethod" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="E-Wallet">E-Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Cost</label>
                            <input type="number" name="total_cost" id="editTotalCost" step="1000" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200" required>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Order
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Update existing displayOrderHistory function
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
            const canModify = status === "On the Way" || status === "On Process";
            
            const orderElement = document.createElement("div");
            orderElement.className = "order-card p-6 hover:bg-gray-50 relative";
            orderElement.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="space-y-3 flex-grow">
                        <div class="flex items-center space-x-3">
                            <span class="status-badge ${statusClass}">${status}</span>
                            <p class="text-gray-500 text-sm">${new Date(order.date).toLocaleDateString()}</p>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-semibold text-gray-800">${order.cleaning_type || 'Standard Cleaning'}</h3>
                            <p class="text-gray-600 text-sm">Duration: ${order.duration || 0} Hours</p>
                            <p class="text-gray-600 text-sm">Payment: ${order.pay_method || 'N/A'}</p>
                            <p class="text-gray-600 text-sm">Amount: ${formatCurrency(order.total_cost || 0)}</p>
                        </div>
                    </div>
                    <div class="text-right flex flex-col space-y-2">
                        <p class="text-sm text-gray-500">Order #${order.id || 'N/A'}</p>
                        ${canModify ? `
                            <div class="flex space-x-2 mt-2">
                                <button onclick="openEditModal(${JSON.stringify(order).replace(/"/g, '&quot;')})" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="deleteOrder(${order.id})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            orderHistoryList.appendChild(orderElement);
        });
    }

    function openEditModal(order) {
        document.getElementById('editOrderId').value = order.id;
        document.getElementById('editName').value = order.name;
        document.getElementById('editEmail').value = order.email;
        document.getElementById('editPhone').value = order.phone;
        document.getElementById('editAddress').value = order.address;
        document.getElementById('editDate').value = order.date.split(' ')[0];
        document.getElementById('editTime').value = order.time;
        document.getElementById('editCleaningType').value = order.cleaning_type;
        document.getElementById('editDuration').value = order.duration;
        document.getElementById('editPaymentMethod').value = order.pay_method;
        document.getElementById('editTotalCost').value = order.total_cost;

        document.getElementById('editOrderModal').classList.remove('hidden');
    }

    function closeEditModal() {
function closeEditModal() {
    document.getElementById('editOrderModal').classList.add('hidden');
}

function deleteOrder(orderId) {
    if (!confirm('Are you sure you want to delete this order?')) return;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'action': 'delete_order',
            'order_id': orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the order from local orderHistory array
            orderHistory = orderHistory.filter(order => order.id !== orderId);
            
            // Refresh the display
            displayOrderHistory();
            updateStats();
            
            // Show success message
            alert('Order successfully deleted');
        } else {
            alert('Failed to delete order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the order');
    });
}

// Event listener for edit order form submission
document.getElementById('editOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action', 'update_order');

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload order history to get updated data
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    // Extract the orderHistory JSON from the response
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const scriptTags = tempDiv.getElementsByTagName('script');
                    for (let script of scriptTags) {
                        if (script.textContent.includes('let orderHistory =')) {
                            // Execute the script to update orderHistory
                            eval(script.textContent.split('let orderHistory =')[1].split(';')[0]);
                            break;
                        }
                    }

                    // Refresh the display
                    displayOrderHistory();
                    updateStats();
                    
                    // Close the modal
                    closeEditModal();
                    
                    // Show success message
                    alert('Order successfully updated');
                });
        } else {
            alert('Failed to update order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the order');
    });
});

// Modify updateOrderStatus function to check for modifiable status
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

// Function to calculate the modifiable status
function isOrderModifiable(order) {
    const status = updateOrderStatus(order);
    return status === "On the Way" || status === "On Process";
}