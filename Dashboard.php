<?php

include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit();
};

// Chat System PHP Handlers
if (isset($_GET['action']) && $_GET['action'] === 'get_messages') {
  header('Content-Type: application/json');
  
  $receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
  $where_clause = "WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)";
  $params = [$admin_id, $receiver_id, $receiver_id, $admin_id];
  
  $query = "SELECT m.*, 
            s.id as sender_id, s.name as sender_name, 
            r.id as receiver_id, r.name as receiver_name 
            FROM messages m 
            JOIN users s ON m.sender_id = s.id 
            JOIN users r ON m.receiver_id = r.id 
            $where_clause
            ORDER BY m.timestamp ASC";
  
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, 'iiii', ...$params);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  
  $messages = [];
  while($row = mysqli_fetch_assoc($result)) {
      $messages[] = [
          'id' => $row['id'],
          'from' => ['id' => $row['sender_id'], 'name' => $row['sender_name']],
          'to' => ['id' => $row['receiver_id'], 'name' => $row['receiver_name']],
          'text' => $row['message'],
          'time' => $row['timestamp'],
          'is_read' => $row['is_read']
      ];
  }
  
  echo json_encode($messages);
  exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'send_message') {
  header('Content-Type: application/json');
  
  $message = trim($_POST['message']);
  $receiver_id = (int)$_POST['receiver_id'];
  
  // Validate receiver
  $user_check = mysqli_query($conn, "SELECT id FROM users WHERE id = '$receiver_id' AND user_type = 'user'");
  if(mysqli_num_rows($user_check) === 0) {
      die(json_encode(['status' => 'error', 'message' => 'Invalid recipient']));
  }
  
  $stmt = mysqli_prepare($conn, "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
  mysqli_stmt_bind_param($stmt, 'iis', $admin_id, $receiver_id, $message);
  
  if(mysqli_stmt_execute($stmt)) {
      echo json_encode(['status' => 'success']);
  } else {
      echo json_encode(['status' => 'error', 'message' => 'Database error']);
  }
  exit;
}

// Get list of users for chat
$users_query = mysqli_query($conn, "SELECT id, name FROM users WHERE user_type = 'user'") or die('query failed');



?>

<?php
// Get orders per month data
$orders_data = [];
$orders_query = mysqli_query($conn, 
    "SELECT 
        DATE_FORMAT(placed_on, '%Y-%m') AS month, 
        COUNT(*) AS order_count 
     FROM orders 
     GROUP BY month 
     ORDER BY month"
);

while($row = mysqli_fetch_assoc($orders_query)) {
    $orders_data[$row['month']] = $row['order_count'];
}

// Fill in missing months with zero values
$start = new DateTime('first day of this year');
$end = new DateTime('last day of this year');
$interval = DateInterval::createFromDateString('1 month');
$period = new DatePeriod($start, $interval, $end);

$chart_labels = [];
$chart_data = [];

foreach ($period as $dt) {
    $month_key = $dt->format("Y-m");
    $chart_labels[] = $dt->format("M");
    $chart_data[] = $orders_data[$month_key] ?? 0;
}
?>

<?php
// Get login counts data
$logins_data = [];
$logins_query = mysqli_query($conn,
    "SELECT 
        DATE(last_login) AS login_date, 
        COUNT(*) AS login_count 
     FROM users 
     WHERE last_login IS NOT NULL 
     GROUP BY login_date"
);

while($row = mysqli_fetch_assoc($logins_query)) {
    $logins_data[$row['login_date']] = $row['login_count'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>

  <link rel="icon" type="image/png" href="images/pharmalogo.png">
  <link rel="stylesheet" href="css/dashboard.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css"/>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

    :root {
        --pharma-blue: #2D9CDB;
        --pharma-green: #27AE60;
        --pharma-red: #EB5757;
        --light-blue: #E3F2FD;
        --sterile-white: #fff;
        --dark-text: #333;
        --border: 2px solid #DCE6F1;
        --box-shadow: 0 5px 15px rgba(41, 128, 185, 0.1);
    }

    * {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color: var(--light-blue);
        color: var(--dark-text);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    body.dark-mode {
        background-color: #121212;
        color: #f5f5f5;
    }

    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 70px;
        background-color: var(--pharma-blue);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        z-index: 1000;
    }

    body.dark-mode .navbar {
        background-color: #1e1e1e;
    }

    .navbar-left {
        display: flex;
        align-items: center;
    }

    .navbar-left h1 {
        color: var(--sterile-white);
        font-size: 1.5rem;
        margin-left: 15px;
    }

    .navbar-right {
        display: flex;
        align-items: center;
    }

    .nav-icon {
        background: none;
        border: none;
        color: var(--sterile-white);
        font-size: 1.5rem;
        cursor: pointer;
        margin-left: 20px;
        position: relative;
    }

    .nav-icon:hover {
        color: var(--pharma-green);
    }

    .notification-bell {
        position: relative;
    }

    .notification-count {
        position: absolute;
        top: -5px;
        right: -10px;
        background-color: var(--pharma-red);
        color: var(--sterile-white);
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 0.8rem;
    }

    .sidebar {
        position: fixed;
        top: 70px;
        left: 0;
        width: 250px;
        height: calc(100vh - 70px);
        background-color: var(--pharma-blue);
        color: white;
        transition: left 0.3s ease;
        z-index: 999;
        overflow-y: auto;
    }

    body.dark-mode .sidebar {
        background-color: #1e1e1e;
    }

    .sidebar-header {
        padding: 20px;
        background-color: var(--pharma-green);
        text-align: center;
    }

    body.dark-mode .sidebar-header {
        background-color: #2e2e2e;
    }

    .sidebar-menu a {
        color: #bdc3c7;
        text-decoration: none;
        padding: 15px 25px;
        display: block;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .sidebar-menu a:hover {
        background-color: var(--pharma-green);
        color: #ecf0f1;
    }

    body.dark-mode .sidebar-menu a {
        color: #bdbdbd;
    }

    body.dark-mode .sidebar-menu a:hover {
        background-color: #333;
        color: #fff;
    }

    .sidebar-menu a i {
        margin-right: 10px;
        width: 20px;
    }

    .main-content {
        margin-left: 250px;
        margin-top: 70px;
        padding: 20px;
        background-color: var(--light-blue);
        min-height: calc(100vh - 70px);
        transition: margin-left 0.3s ease;
    }

    body.dark-mode .main-content {
        background-color: #1e1e1e;
    }

    .stat-card {
        background: var(--sterile-white);
        border-radius: 10px;
        padding: 20px;
        box-shadow: var(--box-shadow);
        border: var(--border);
        transition: all 0.3s ease;
    }

    body.dark-mode .stat-card {
        background-color: #2e2e2e;
        border-color: #3e3e3e;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(41, 128, 185, 0.15);
    }

    .stat-card h6 {
        color: var(--pharma-blue);
    }

    body.dark-mode .stat-card h6 {
        color: var(--pharma-blue);
    }

    .stat-card h2 {
        color: var(--dark-text);
    }

    body.dark-mode .stat-card h2 {
        color: #f5f5f5;
    }

    .chart-container {
        background: var(--sterile-white);
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: var(--box-shadow);
    }

    body.dark-mode .chart-container {
        background-color: #2e2e2e;
    }

    .todo-list {
        background: var(--sterile-white);
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: var(--box-shadow);
    }

    body.dark-mode .todo-list {
        background-color: #2e2e2e;
    }

    .todo-list ul {
        list-style: none;
        padding: 0;
    }

    .todo-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    body.dark-mode .todo-list li {
        border-bottom: 1px solid #444;
    }

    .todo-list li:last-child {
        border-bottom: none;
    }

    .todo-list .delete-todo {
        color: var(--pharma-red);
        cursor: pointer;
    }

    .login-count {
    position: absolute;
    top: 2px;
    right: 2px;
    background: var(--pharma-green);
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

    :root {
            --primary-color: #4a76a8;
            --primary-light: #6292c9;
            --primary-dark: #3a5d87;
            --text-light: #ffffff;
            --text-dark: #333333;
            --bg-light: #f9f9f9;
            --bg-gray: #f0f2f5;
            --message-sent: #dcf8c6;
            --message-received: #ffffff;
            --border-color: #e1e4e8;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Chat Toggle Button */
        .chat-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow);
            z-index: 1000;
            transition: all 0.2s ease;
        }

        .chat-toggle .notification-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff0000;
    color: white;
    padding: 2px 5px;
    border-radius: 50%;
    font-size: 10px;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    border: 1px solid rgba(255,255,255,0.3);
    animation: pulse 1.5s infinite;
    box-shadow: 0 0 5px rgba(255,0,0,0.5);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

        .chat-toggle:hover {
            background-color: var(--primary-dark);
            transform: scale(1.05);
        }

        .chat-toggle i {
            font-size: 24px;
        }

        /* Chat Section Container */
        .chat-section {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            display: none;
            z-index: 999;
        }

        .chat-section.active {
            display: block;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Chat Container */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 500px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background-color: var(--bg-light);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        /* Chat Header */
        .chat-header {
            padding: 15px;
            background-color: var(--primary-color);
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .close-chat {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 18px;
        }

        .user-select {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background-color: var(--text-light);
            font-size: 14px;
            cursor: pointer;
        }

        /* Chat Messages Area */
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: var(--bg-gray);
            display: flex;
            flex-direction: column;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background-color: #c1c1c1;
            border-radius: 6px;
        }

        .message {
            padding: 10px 12px;
            margin: 4px 0;
            border-radius: 16px;
            max-width: 80%;
            word-wrap: break-word;
            position: relative;
            clear: both;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .sent {
            background-color: var(--message-sent);
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .received {
            background-color: var(--message-received);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .content {
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .meta {
            font-size: 11px;
            color: #888;
            text-align: right;
        }

        .empty-state {
            text-align: center;
            color: #888;
            margin-top: auto;
            margin-bottom: auto;
            font-style: italic;
        }

        /* Chat Input Area */
        .chat-input {
            padding: 10px;
            background-color: var(--text-light);
            border-top: 1px solid var(--border-color);
        }

        .message-form {
            display: flex;
            gap: 8px;
        }

        .message-input {
            flex: 1;
            padding: 12px 15px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            outline: none;
            font-size: 14px;
        }

        .message-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 118, 168, 0.2);
        }

        .send-button {
            background-color: var(--primary-color);
            color: var(--text-light);
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .send-button:hover {
            background-color: var(--primary-dark);
        }

        .send-button i {
            font-size: 16px;
        }

        /* Status indicator */
        .status-indicator {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 12px;
            color: #888;
        }

        /* User is typing indicator */
        .typing-indicator {
            padding: 8px 12px;
            background-color: rgba(0,0,0,0.05);
            border-radius: 10px;
            font-size: 12px;
            color: #666;
            margin: 5px 0;
            align-self: flex-start;
            display: none;
        }

        .typing-indicator.active {
            display: block;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }

        /* Fade transition for new messages */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message {
            animation: fadeIn 0.3s ease;
        }

        /* Chat notification badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff4757;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            display: none;
        }

        .notification-badge.active {
            display: flex;
        }


              /* Account Box Styling */
              .navbar .navbar-right .account-box {
  position: absolute;
  top: 60px;
  right: 2rem;
  background-color: white;
  border: 1px solid #DCE6F1;
  text-align: center;
  box-shadow: 0 5px 15px rgba(41, 128, 185, 0.15);
  padding: 1.5rem;
  border-radius: 10px;
  width: 22rem;
  display: none;
  animation: fadeIn .3s ease;
  z-index: 1001;
}

.navbar .navbar-right .account-box.active {
  display: block;
}

.navbar .navbar-right .account-box::before {
  content: '';
  position: absolute;
  top: -10px;
  right: 1.5rem;
  width: 20px;
  height: 20px;
  background: white;
  transform: rotate(45deg);
  border-left: 1px solid #DCE6F1;
  border-top: 1px solid #DCE6F1;
}

.navbar .navbar-right .account-box .user-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 1rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}

.navbar .navbar-right .account-box .user-avatar {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background-color: var(--pharma-blue);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
}

.navbar .navbar-right .account-box .user-avatar i {
  font-size: 2rem;
  color: white;
}

.navbar .navbar-right .account-box h4 {
  margin: 0.5rem 0;
  color: var(--dark-text);
  font-weight: 600;
}

.navbar .navbar-right .account-box .user-details {
  text-align: left;
  margin-bottom: 1.5rem;
}

.navbar .navbar-right .account-box .detail-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.8rem;
}

.navbar .navbar-right .account-box .detail-item i {
  color: var(--pharma-blue);
  margin-right: 1rem;
  width: 20px;
  text-align: center;
}

.navbar .navbar-right .account-box .detail-item p {
  margin: 0;
  color: var(--dark-text);
  font-size: 0.95rem;
}

.navbar .navbar-right .account-box .detail-item p span {
  font-weight: 500;
  color: var(--pharma-blue);
}

.navbar .navbar-right .account-box .logout-btn {
  display: inline-block;
  background-color: var(--pharma-red);
  color: white;
  padding: 0.6rem 1.5rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
}

.navbar .navbar-right .account-box .logout-btn:hover {
  background-color: #d63031;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(235, 87, 87, 0.3);
}

.navbar .navbar-right .account-box .account-footer {
  font-size: 0.8rem;
  color: #888;
  margin-top: 1rem;
}

/* Dark mode styles */
body.dark-mode .navbar .navbar-right .account-box {
  background-color: #2e2e2e;
  border-color: #3e3e3e;
}

body.dark-mode .navbar .navbar-right .account-box::before {
  background-color: #2e2e2e;
  border-color: #3e3e3e;
}

body.dark-mode .navbar .navbar-right .account-box h4,
body.dark-mode .navbar .navbar-right .account-box .detail-item p {
  color: #f5f5f5;
}

body.dark-mode .navbar .navbar-right .account-box .user-header {
  border-color: #3e3e3e;
}

body.dark-mode .navbar .navbar-right .account-box .detail-item i,
body.dark-mode .navbar .navbar-right .account-box .detail-item p span {
  color: var(--pharma-blue);
}

/* Animation for account box */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
  </style>
</head>
<body>
  <div class="navbar">
    <div class="navbar-left">
      <button id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      <h1>Admin Dashboard</h1>
    </div>
    <div class="navbar-right">
      <button id="themeToggle" class="nav-icon">
        <i class="fas fa-moon"></i>
      </button>
      <div class="nav-icon notification-bell">
        <i class="fas fa-bell"></i>
        <span class="notification-count">3</span>
      </div>

      <div class="nav-icon notification-bell">
        <i id="user-btn" class="fas fa-user"></i>
      </div>

      <div class="account-box">
  <div class="user-header">
    <div class="user-avatar">
      <i class="fas fa-user"></i>
    </div>
    <h4><?php echo $_SESSION['admin_name']; ?></h4>
  </div>
  <div class="user-details">
    <div class="detail-item">
      <i class="fas fa-id-badge"></i>
      <p>Role: <span>Administrator</span></p>
    </div>
    <div class="detail-item">
      <i class="fas fa-envelope"></i>
      <p>Email: <span><?php echo $_SESSION['admin_email']; ?></span></p>
    </div>
  </div>
  <a href="logout.php" class="logout-btn">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
  <div class="account-footer">
    Logged in as administrator
  </div>
</div>
    </div>
  </div>

  <div class="sidebar">
    <div class="sidebar-header">
      <h3 class="mb-0">
        <i class="fas fa-hospital"></i> Menu
      </h3>
    </div>
    <div class="sidebar-menu">
      <a href="Dashboard.php">
        <i class="fas fa-home"></i> Dashboard
      </a>
      <a href="admin_products.php">
        <i class="fas fa-tablets"></i> Products
      </a>
      <a href="admin_orders.php">
        <i class="fas fa-shopping-cart"></i> Orders
      </a>
      <a href="admin_users.php">
        <i class="fas fa-users"></i> Users
      </a>
      <a href="admin_contacts.php">
        <i class="fas fa-envelope"></i> Messages
      </a>
      <a href="logout.php">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <div class="main-content" id="mainContent">
    <div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
          <h6 class="text-muted">Total Pendings</h6>
          <?php
            $total_pendings = 0;
            $select_pendings = mysqli_query($conn, "SELECT * FROM `orders` WHERE payment_status = 'pending'") or die('query failed');
            while($fetch_pendings = mysqli_fetch_assoc($select_pendings)){
               $total_pendings += $fetch_pendings['total_price'];
            };
         ?>
          <h2><?php echo $total_pendings; ?> FCFA</h2>
          <span class="text-primary"
            ><i class="fas fa-chart-line"></i> 8%</span
          >
        </div>
      </div>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
          <h6 class="text-muted">Completed Payments</h6>
          <?php
            $total_completes = 0;
            $select_completes = mysqli_query($conn, "SELECT * FROM `orders` WHERE payment_status = 'completed'") or die('query failed');
            while($fetch_completes = mysqli_fetch_assoc($select_completes)){
               $total_completes += $fetch_completes['total_price'];
            };
         ?>
         
          <h2><?php echo $total_completes; ?> FCFA</h2>
          <span class="text-primary"
            ><i class="fas fa-chart-line"></i> 8%</span
          >
        </div>
      </div>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
          <h6 class="text-muted">Orders Placed</h6>
          <?php
            $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
            $number_of_orders = mysqli_num_rows($select_orders);
         ?>
          <h2><?php echo $number_of_orders; ?></h2>
          <span class="text-danger"
            ><i class="fas fa-arrow-down"></i> 3%</span
          >
        </div>
      </div>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
          <h6 class="text-muted">Product Added</h6>
          <?php
            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            $number_of_products = mysqli_num_rows($select_products);
         ?>
          <h2><?php echo $number_of_products; ?></h2>
          <span class="text-success"
            ><i class="fas fa-users"></i> 24%</span
          >
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <h6 class="text-muted">Normal Users</h6>
            <?php
            $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
            $number_of_users = mysqli_num_rows($select_users);
         ?>
            <h2><?php echo $number_of_users; ?></h2>
            <span class="text-success"
              ><i class="fas fa-arrow-up"></i> 12%</span
            >
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <h6 class="text-muted">Admin Users</h6>
            <?php
            $select_admin = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin'") or die('query failed');
            $number_of_admin = mysqli_num_rows($select_admin);
         ?>
            <h2><?php echo $number_of_admin; ?></h2>
            <span class="text-primary"
              ><i class="fas fa-chart-line"></i> 8%</span
            >
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <h6 class="text-muted">Total Accounts</h6>
            <?php
            $select_account = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
            $number_of_account = mysqli_num_rows($select_account);
         ?>
            <h2><?php echo $number_of_account; ?></h2>
            <span class="text-danger"
              ><i class="fas fa-arrow-down"></i> 3%</span
            >
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <h6 class="text-muted">New Messages</h6>
            <?php
            $select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
            $number_of_messages = mysqli_num_rows($select_messages);
         ?>
            <h2><?php echo $number_of_messages; ?></h2>
            <span class="text-success"
              ><i class="fas fa-users"></i> 24%</span
            >
          </div>
        </div>
      </div>

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="chart-container">
          <canvas id="mainChart"></canvas>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="chart-container">
          <h6>Recent Activity</h6>
          <ul class="list-group">
            <li class="list-group-item d-flex align-items-center">
              <i class="fas fa-user-plus text-success me-2"></i>
              New user registration
            </li>
            <li class="list-group-item d-flex align-items-center">
              <i class="fas fa-shopping-cart text-primary me-2"></i>
              New order #1234
            </li>
            <li class="list-group-item d-flex align-items-center">
              <i class="fas fa-exclamation-triangle text-warning me-2"></i>
              System update required
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12 col-lg-8">
        <div class="chart-container">
          <h6>Calendar</h6>
          <div id="calendar"></div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="todo-list">
          <h6>To-Do List</h6>
          <input
            type="text"
            id="todoInput"
            class="form-control mb-3"
            placeholder="Add a new task"
          />
          <ul id="todoList"></ul>
        </div>
      </div>
    </div>


    <!-- Chat Section -->

    <div class="chat-toggle" id="chat-toggle">
    <i class="fas fa-comments"></i>
    <?php
    // Get unread message count
    $unread_query = mysqli_query($conn, "SELECT COUNT(*) as unread FROM messages WHERE receiver_id = '$admin_id' AND is_read = 0");
    $unread_count = mysqli_fetch_assoc($unread_query)['unread'];
    ?>
    <?php if($unread_count > 0): ?>
        <span class="notification-count"><?php echo $unread_count; ?></span>
    <?php endif; ?>
</div>

<section class="chat-section">
    <div class="chat-container">
        <div class="chat-header">
            <h2>User Chat</h2>
            <select id="receiver-select" class="user-select">
                <option value="">Select a user to chat with</option>
                <?php while($user = mysqli_fetch_assoc($users_query)): ?>
                    <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <p style='text-align: center; color: #888;'>Select a user to start chatting</p>
        </div>
        
        <div class="chat-input">
            <form id="message-form" class="message-form">
                <input type="hidden" id="receiver-id">
                <input type="text" id="message-input" class="message-input" placeholder="Type a message..." required>
                <button type="submit" class="send-button">Send</button>
            </form>
        </div>
    </div>
</section>

  </div>

  </div>

  </div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

  <script>

// Auto-scroll to bottom
const messageList = document.querySelector('.message-list');
if(messageList) {
    messageList.scrollTop = messageList.scrollHeight;
}

// Auto-resize textarea
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

// Dark/Bright Mode Toggle
const themeToggle = document.getElementById('themeToggle');
const body = document.body;
const themeIcon = themeToggle.querySelector('i');

// Check localStorage for theme preference
if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark-mode');
    themeIcon.classList.remove('fa-moon');
    themeIcon.classList.add('fa-sun');
}

// Toggle theme on button click
themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    if (body.classList.contains('dark-mode')) {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        localStorage.setItem('theme', 'dark');
    } else {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        localStorage.setItem('theme', 'light');
    }
});

// Sidebar Toggle
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.querySelector('.sidebar');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});

// Account Box Toggle
let userBtn = document.querySelector('#user-btn');
let accountBox = document.querySelector('.account-box');

userBtn.addEventListener('click', () => {
    accountBox.classList.toggle('active');
});

// Close account box when clicking outside
document.addEventListener('click', (e) => {
    if (!userBtn.contains(e.target) && !accountBox.contains(e.target)) {
        accountBox.classList.remove('active');
    }
});

// Other JavaScript code remains the same
const ctx = document.getElementById("mainChart").getContext("2d");
new Chart(ctx, {
    type: "line",
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: "Orders Per Month",
            data: <?php echo json_encode($chart_data); ?>,
            borderColor: "#2c3e50",
            tension: 0.4,
        }]
    },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: "top",
      },
    },
  },
});

document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    
    // Create the loginCounts object from PHP data
    const loginCounts = <?php 
        $login_data_json = [];
        foreach($logins_data as $date => $count) {
            $login_data_json[$date] = $count;
        }
        echo json_encode($login_data_json); 
    ?>;
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        events: Object.entries(loginCounts).map(([date, count]) => ({
            start: date,
            display: 'background',
            title: `${count} logins`,
        })),
        dayCellContent: function(args) {
            const dateStr = args.date.toISOString().split('T')[0];
            const count = loginCounts[dateStr] || 0;
            return {
                html: `<div class="fc-day-number">${args.dayNumberText}</div>
                      ${count > 0 ? `<div class="login-count">${count}</div>` : ''}`
            };
        },
    });
    calendar.render();
});
const todoInput = document.getElementById("todoInput");
const todoList = document.getElementById("todoList");

todoInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter" && todoInput.value.trim() !== "") {
    const todoItem = document.createElement("li");
    todoItem.innerHTML = `
      ${todoInput.value}
      <span class="delete-todo"><i class="fas fa-trash"></i></span>
    `;
    todoList.appendChild(todoItem);
    todoInput.value = "";

    todoItem.querySelector(".delete-todo").addEventListener("click", function () {
      todoItem.remove();
    });
  }
});

    // Chat System JavaScript
    const chatToggle = document.getElementById('chat-toggle');
const chatSection = document.querySelector('.chat-section');
const userId = <?= $admin_id ?>;

chatToggle.addEventListener('click', () => {
    chatSection.classList.toggle('active');
    if (chatSection.classList.contains('active')) {
        scrollToBottom();
    }
});

document.addEventListener('click', (e) => {
    if (!chatSection.contains(e.target) && !chatToggle.contains(e.target) && chatSection.classList.contains('active')) {
        chatSection.classList.remove('active');
    }
});

// Message handling functions
function renderMessages(messages) {
    const container = document.getElementById('chat-messages');
    container.innerHTML = '';
    
    messages.forEach(msg => {
        const div = document.createElement('div');
        div.className = `message ${msg.from.id == userId ? 'sent' : 'received'}`;
        div.innerHTML = `
            <div class="content">${msg.text}</div>
            <div class="meta">${new Date(msg.time).toLocaleTimeString()}</div>
        `;
        container.appendChild(div);
    });
    
    if(messages.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #888;">No messages yet</p>';
    }
    
    scrollToBottom();
}

async function fetchMessages(receiverId) {
    try {
        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        
        const response = await fetch('Dashboard.php?action=get_messages', {
            method: 'POST',
            body: formData
        });
        
        const messages = await response.json();
        renderMessages(messages);
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

document.getElementById('receiver-select').addEventListener('change', function() {
    const receiverId = this.value;
    // Add this line to update the hidden input
    document.getElementById('receiver-id').value = receiverId;
    
    if (receiverId) {
        fetchMessages(receiverId);
        startPolling(receiverId);
    }
});

// Start polling every 2 seconds
let pollInterval;
function startPolling(receiverId) {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(() => fetchMessages(receiverId), 2000);
}

document.getElementById('message-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = document.getElementById('message-input').value.trim();
    // Get receiver ID directly from the select element
    const receiverId = document.getElementById('receiver-select').value;
    
    if (!receiverId || !message) {
        alert('Please select a user and enter a message');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('message', message);
        formData.append('receiver_id', receiverId);
        
        const response = await fetch('Dashboard.php?action=send_message', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.status === 'success') {
            document.getElementById('message-input').value = '';
            fetchMessages(receiverId);
        } else {
            alert('Error: ' + (result.message || 'Failed to send message'));
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Network error - please try again');
    }
});

// Add to existing JavaScript
let currentReceiverId = null;

// Function to update notification badge
async function updateNotificationBadge() {
    try {
        const response = await fetch('get_unread_count.php');
        const count = await response.text();
        const badge = document.querySelector('.chat-toggle .notification-count');
        
        if (count > 0) {
            if (!badge) {
                const newBadge = document.createElement('span');
                newBadge.className = 'notification-count';
                newBadge.textContent = count;
                document.querySelector('.chat-toggle').appendChild(newBadge);
            } else {
                badge.textContent = count;
            }
        } else if (badge) {
            badge.remove();
        }
    } catch (error) {
        console.error('Error updating badge:', error);
    }
}

// Modify message fetch to mark messages as read
async function fetchMessages(receiverId) {
    try {
        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        
        const response = await fetch('Dashboard.php?action=get_messages', {
            method: 'POST',
            body: formData
        });
        
        const messages = await response.json();
        renderMessages(messages);
        
        // Mark messages as read
        if (receiverId) {
            await fetch('mark_as_read.php', {
                method: 'POST',
                body: new FormData(document.getElementById('message-form'))
            });
            updateNotificationBadge();
        }
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

// Update polling function
function startPolling(receiverId) {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(async () => {
        await fetchMessages(receiverId);
        await updateNotificationBadge(); // Add this line
    }, 2000);
}

</script>

  </script>

 </body>
</html>