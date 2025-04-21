<?php
session_start();

$popupMessage = "";
$popupClass = "";
$showPopup = false;

// Show popup if message is set in session (from previous POST)
if (isset($_SESSION['popup_message'])) {
    $popupMessage = $_SESSION['popup_message'];
    $popupClass = $_SESSION['popup_class'];
    $showPopup = true;

    // Clear message so it doesn't persist on refresh
    unset($_SESSION['popup_message']);
    unset($_SESSION['popup_class']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (strlen($phone) !== 10 || !ctype_digit($phone)) {
        $_SESSION['popup_message'] = "Phone number must be exactly 10 digits.";
        $_SESSION['popup_class'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (empty($name) || empty($subject) || empty($phone) || empty($email) || empty($message)) {
        $_SESSION['popup_message'] = "Please fill in all fields.";
        $_SESSION['popup_class'] = "error";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $conn = new mysqli('localhost', 'root', '', 'my_projects');
        if ($conn->connect_error) {
            $_SESSION['popup_message'] = "Database connection failed.";
            $_SESSION['popup_class'] = "error";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $stmt = $conn->prepare("INSERT INTO DEMO (name, subject, phone, email, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $subject, $phone, $email, $message);

            if ($stmt->execute()) {
                $_SESSION['popup_message'] = "Message sent successfully!";
                $_SESSION['popup_class'] = "success";
            } else {
                $_SESSION['popup_message'] = "Something went wrong. Please try again.";
                $_SESSION['popup_class'] = "error";
            }

            $stmt->close();
            $conn->close();

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #2C3E50, #4CA1AF);  
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5, #FFB6C1);
            padding: 30px;
            width: 400px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            background-color:rgba(201, 211, 126, 0.4); /* AliceBlue */
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        #popup {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 8px;
    font-weight: bold;
    z-index: 9999;
    color: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    animation: fadeOut 5s forwards;
}

.success {
    background-color: #4CAF50;
}

.error {
    background-color: #f44336;
}

@keyframes fadeOut {
    0%, 85% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        pointer-events: none;
    }
}

    </style>
</head>
<body>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $showPopup): ?>
    <div id="popup" class="<?php echo $popupClass; ?>">
        <?php echo $popupMessage; ?>
    </div>
<?php endif; ?>


<div class="form-container">
    <h2>Contact Form</h2>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="text" name="subject" placeholder="Subject" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" required></textarea>
        <button type="submit">Send</button>
    </form>
</div>
<input type="hidden" name="form_submitted" value="1">

<script>
window.onload = function () {
    const popup = document.getElementById("popup");
    if (popup && popup.style.display === "block") {
        setTimeout(() => {
            popup.style.display = "none";
        }, 5000); // Hide after 5 seconds
    }
}
</script>


</body>
</html>
