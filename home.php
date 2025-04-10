<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['add_to_wishlist'])){

   $product_id = $_POST['product_id'];
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   
   $check_wishlist_numbers = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_wishlist_numbers) > 0){
       $message[] = 'already added to wishlist';
   }elseif(mysqli_num_rows($check_cart_numbers) > 0){
       $message[] = 'already added to cart';
   }else{
       mysqli_query($conn, "INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_image')") or die('query failed');
       $message[] = 'product added to wishlist';
   }

}

if(isset($_POST['add_to_cart'])){

   $product_id = $_POST['product_id'];
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart_numbers) > 0){
       $message[] = 'already added to cart';
   }else{

       $check_wishlist_numbers = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

       if(mysqli_num_rows($check_wishlist_numbers) > 0){
           mysqli_query($conn, "DELETE FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
       }

       mysqli_query($conn, "INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
       $message[] = 'product added to cart';
   }

}

if (isset($_GET['action']) && $_GET['action'] === 'get_messages') {
   header('Content-Type: application/json');
   
   $receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
   $where_clause = "WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)";
   $params = [$user_id, $receiver_id, $receiver_id, $user_id];
   
   if ($receiver_id === 0) {
       $where_clause = "WHERE m.sender_id = ? OR m.receiver_id = ?";
       $params = [$user_id, $user_id];
   }
   
   $query = "SELECT m.*, 
             s.id as sender_id, s.name as sender_name, 
             r.id as receiver_id, r.name as receiver_name 
             FROM messages m 
             JOIN users s ON m.sender_id = s.id 
             JOIN users r ON m.receiver_id = r.id 
             $where_clause
             ORDER BY m.timestamp ASC";
             
   $stmt = mysqli_prepare($conn, $query);
   mysqli_stmt_bind_param($stmt, str_repeat("i", count($params)), ...$params);
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

// AJAX endpoint to send a message
if (isset($_GET['action']) && $_GET['action'] === 'send_message') {
   header('Content-Type: application/json');
   
   $message = trim($_POST['message']);
   $receiver_id = (int)$_POST['receiver_id'];
   
   if (!empty($message) && $receiver_id > 0) {
       $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
       $stmt = mysqli_prepare($conn, $query);
       mysqli_stmt_bind_param($stmt, "iis", $user_id, $receiver_id, $message);
       
       if(mysqli_stmt_execute($stmt)) {
           echo json_encode(['status' => 'success']);
       } else {
           echo json_encode(['status' => 'error', 'message' => 'Database error']);
       }
   } else {
       echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty or receiver not selected']);
   }
   exit;
}

// Get list of other users for chat
$users_query = mysqli_query($conn, "SELECT id, name FROM users WHERE user_type = 'admin'") or die('query failed');
?>




<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <link rel="icon" type="image/png" href="images/pharmalogo.png">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">
   <style>
    .slider-container {
        position: relative;
        width: 100%;
        height: 600px;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .slide {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
    }

    .slide.active {
        opacity: 1;
    }

    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .slider-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #fff;
        z-index: 2;
    }

    .slider-content h3 {
      font-size: 4rem;
    }

    .slider-content p {
      font-size: 2rem;
    }

    .slider-indicators {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
    }

    .slider-indicators span {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        margin: 0 5px;
        cursor: pointer;
    }

    .slider-indicators span.active {
        background: #fff;
    }

    .chat-container {
    display: flex;
    flex-direction: column;
    height: 500px;
    width: 350px;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.chat-header {
    padding: 15px;
    background-color: #4a76a8;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background-color: #f9f9f9;
}
.chat-input {
    padding: 15px;
    background-color: #f0f0f0;
    border-top: 1px solid #ddd;
}
.message {
    padding: 12px 18px;
    margin-bottom: 15px;
    border-radius: 18px;
    max-width: 70%;
    word-wrap: break-word;
    position: relative;
    clear: both;
    font-size: 2.1em;
}
.sent {
    background-color: #dcf8c6;
    float: right;
    border-bottom-right-radius: 5px;
}
.received {
    background-color: #ffffff;
    float: left;
    border-bottom-left-radius: 5px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
.message .meta {
    font-size: 0.8em;
    color: #888;
    margin-top: 2px;
    text-align: right;
    opacity: 0.8;
}
.date-header {
    text-align: center;
    margin: 25px 0;
    clear: both;
    position: relative;
    font-size: 1em;
}
.date-header span {
    background: #f9f9f9;
    padding: 0 15px;
    color: #666;
    font-size: 1.1em;
    font-weight: bold;
    position: relative;
    z-index: 1;
}
.date-header:after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: #ddd;
    z-index: 0;
}
.message-form {
    display: flex;
}
.message-input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 20px;
    margin-right: 10px;
}
.send-button {
    background-color: #4a76a8;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
}
.send-button:hover {
    background-color: #3a5b82;
}
.typing-indicator {
    padding: 10px;
    color: #888;
    font-style: italic;
    display: none;
}
.user-select {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ddd;
    background-color: white;
    color: #333;
}
.chat-toggle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #4a76a8;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 1000;
}
.chat-toggle i {
    font-size: 24px;
}
.chat-toggle:hover {
    background-color: #3a5b82;
}
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
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

    @media (max-width: 768px) {
        .slider-container {
            height: 300px;
        }
    }
   </style>
</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="home">
   <div class="slider-container">
       <div class="slide active">
           <img src="images/bg.png" alt="Slide 1">
       </div>
       <div class="slide">
           <img src="images/back.png" alt="Slide 2">
       </div>
       <div class="slide">
           <img src="images/pharmar.png" alt="Slide 3">
       </div>
       
       <div class="slider-content">
           <h3>The Ages Pharma<br>Health on Hand, Anytime, Anywhere</h3>
           <p>The Ages Pharma embodies a commitment to accessible healthcare with its innovative approach, promising health solutions that are available "On Hand, Anytime, Anywhere." This mission is underpinned by a customer-centric philosophy that seeks to break down traditional barriers to medical access and convenience. Beyond just selling pharmaceuticals, the brand positions itself as a responsive, ubiquitous health partner that understands and adapts to the diverse and dynamic healthcare needs of its customers.</p>
           <a href="about.php" class="btn">discover more</a>
       </div>
       
       <div class="slider-indicators"></div>
   </div>
</section>


<section class="products">

   <h1 class="title">latest products</h1>

   <div class="box-container">

      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 6") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
      <form action="" method="POST" class="box">
         <a href="view_page.php?pid=<?php echo $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <div class="price"><?php echo $fetch_products['price']; ?> FCFA</div>
         <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="" class="image">
         <div class="name"><?php echo $fetch_products['name']; ?></div>
         <input type="number" name="product_quantity" value="1" min="0" class="qty">
         <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
         <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
         <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
         <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
         <input type="submit" value="add to wishlist" name="add_to_wishlist" class="option-btn">
         <input type="submit" value="add to cart" name="add_to_cart" class="btn">
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>

   </div>

   <div class="more-btn">
      <a href="shop.php" class="option-btn">load more</a>
   </div>

</section>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/firm.jpeg" alt="">
      </div>

      <div class="content">
         <h3>about us</h3>
         <p>We are a pioneering healthcare organization dedicated to reimagining medical services through innovative digital platforms that bridge the gap between patient needs and comprehensive healthcare solutions. Founded by a multidisciplinary team of healthcare professionals, technology experts, and patient advocates, we are committed to transforming the traditional healthcare experience by providing accessible, personalized, and technology-driven medical support. Our mission extends beyond mere service delivery – we aim to empower individuals with knowledge, convenience, and exceptional care, making quality healthcare a seamless and supportive journey for every patient.</p>
         <a href="about.php" class="btn">read more</a>
      </div>

   </div>

</section>

<section class="home-contact">

   <div class="content">
      <h3>have any questions?</h3>
      <p>We understand that navigating healthcare services can be complex, and our dedicated support team is always ready to provide clear, comprehensive answers to your most pressing medical and service-related inquiries. Our customer support channels are designed to offer personalized guidance, whether you're seeking information about medication, delivery processes, product details, or need assistance with your healthcare journey. We believe in transparent communication and are committed to ensuring you feel fully informed, supported, and confident in the healthcare solutions we provide.</p>
      <a href="contact.php" class="btn">contact us</a>
   </div>

</section>

<div class="chat-toggle" id="chat-toggle">
    <i class="fas fa-comments"></i>
</div>

<section class="chat-section">
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat Messages</h2>
            <select id="receiver-select" class="user-select">
                <option value="">Select an admin to chat with</option>
                <?php 
                    $users_query = mysqli_query($conn, "SELECT id, name FROM users WHERE user_type = 'admin'") or die('query failed');
                    while($user = mysqli_fetch_assoc($users_query)): 
                ?>
                    <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <p style='text-align: center; color: #888;'>Select a user to start chatting</p>
        </div>
        
        <div class="typing-indicator" id="typing-indicator">
            Someone is typing...
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

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const indicatorsContainer = document.querySelector('.slider-indicators');
    let currentSlide = 0;
    
    slides.forEach((_, index) => {
        const indicator = document.createElement('span');
        indicator.addEventListener('click', () => goToSlide(index));
        indicatorsContainer.appendChild(indicator);
    });
    
    const indicators = document.querySelectorAll('.slider-indicators span');
    indicators[0].classList.add('active');

    function goToSlide(n) {
        slides[currentSlide].classList.remove('active');
        indicators[currentSlide].classList.remove('active');
        currentSlide = (n + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
        indicators[currentSlide].classList.add('active');
    }

    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    // Auto-advance every 5 seconds
    setInterval(nextSlide, 5000);
});
</script>

<script>
    const chatToggle = document.getElementById('chat-toggle');
    const chatSection = document.querySelector('.chat-section');

    chatToggle.addEventListener('click', () => {
        chatSection.classList.toggle('active');
        if (chatSection.classList.contains('active')) {
            scrollToBottom();
        }
    });

    // Close chat when clicking outside
    document.addEventListener('click', (e) => {
        if (!chatSection.contains(e.target) && 
            !chatToggle.contains(e.target) && 
            chatSection.classList.contains('active')) {
            chatSection.classList.remove('active');
        }
    });

    const userId = <?php echo $user_id; ?>;
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const typingIndicator = document.getElementById('typing-indicator');
    const receiverSelect = document.getElementById('receiver-select');
    const receiverIdInput = document.getElementById('receiver-id');
    
    let currentReceiverId = 0;
    let lastMessageTime = 0;

    function renderMessages(messages) {
        chatMessages.innerHTML = '';
        
        if (messages.length === 0) {
            chatMessages.innerHTML = "<p style='text-align: center; color: #888;'>No messages yet. Start the conversation!</p>";
            return;
        }

        let currentDate = '';
        messages.forEach(msg => {
            const messageDate = new Date(msg.time);
            const dateStr = messageDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            const timeStr = messageDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            
            if (dateStr !== currentDate) {
                currentDate = dateStr;
                chatMessages.innerHTML += `
                    <div class="date-header">
                        <span>${dateStr}</span>
                    </div>
                `;
            }
            
            const messageClass = (msg.from.id == userId) ? 'sent' : 'received';
            
            const messageHTML = `
                <div class="message ${messageClass} clearfix">
                    <div class="content">${escapeHTML(msg.text)}</div>
                    <span class="meta">${timeStr}</span>
                </div>
            `;
            
            chatMessages.innerHTML += messageHTML;
        });
        
        scrollToBottom();
    }

    function escapeHTML(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function fetchMessages() {
        if (!currentReceiverId) {
            return; // Don't fetch if no recipient is selected
        }
        
        const formData = new FormData();
        formData.append('receiver_id', currentReceiverId);
        
        fetch('message.php?action=get_messages', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(messages => {
            renderMessages(messages);
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
        });
    }

    function sendMessage(message, receiverId) {
        const formData = new FormData();
        formData.append('message', message);
        formData.append('receiver_id', receiverId);
        
        fetch('message.php?action=send_message', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                messageInput.value = '';
                fetchMessages();
            } else {
                alert('Error sending message: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
        });
    }

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        const receiverId = receiverIdInput.value;
        
        if (!receiverId) {
            alert('Please select a user to chat with');
            return;
        }
        
        if (message) {
            sendMessage(message, receiverId);
        }
    });

    receiverSelect.addEventListener('change', function() {
        currentReceiverId = this.value;
        receiverIdInput.value = this.value;
        
        if (this.value) {
            fetchMessages();
            // Start polling for this user
            startPolling();
        } else {
            chatMessages.innerHTML = "<p style='text-align: center; color: #888;'>Select a user to start chatting</p>";
            // Stop polling when no user is selected
            if (window.pollInterval) {
                clearInterval(window.pollInterval);
            }
        }
    });

    function startPolling() {
        // Clear any existing interval first
        if (window.pollInterval) {
            clearInterval(window.pollInterval);
        }
        
        // Fetch messages initially
        fetchMessages();
        
        // Set up polling every 2 seconds
        window.pollInterval = setInterval(fetchMessages, 2000);
    }
</script>

</body>
</html>