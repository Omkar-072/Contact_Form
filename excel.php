<?php
// Required for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
error_reporting(1);

$conn = new mysqli('localhost', 'root', '', 'my_projects');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// EXPORT TO EXCEL
if (isset($_POST['export'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="contact_records.xls"');
    header('Cache-Control: max-age=0');

    $output = fopen("php://output", "w");

    echo "ID\tName\tSubject\tPhone\tEmail\tMessage\n";

    $result = $conn->query("SELECT * FROM DEMO");
    while ($row = $result->fetch_assoc()) {
        $message = str_replace(["\r", "\n", "\t"], ' ', $row['message']);
        echo $row['id'] . "\t" .
             $row['name'] . "\t" .
             $row['subject'] . "\t" .
             $row['phone'] . "\t" .
             $row['email'] . "\t" .
             $message . "\n";
    }

    fclose($output);
    exit();
}

// MAIL WITH ATTACHMENT
if (isset($_POST['mail'])) {
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

    $filename = "contact_records.xls";
    $file = fopen($filename, "w");

    fwrite($file, "ID\tName\tSubject\tPhone\tEmail\tMessage\n");

    $result = $conn->query("SELECT * FROM DEMO");
    while ($row = $result->fetch_assoc()) {
        $message = str_replace(["\r", "\n", "\t"], ' ', $row['message']);
        fwrite($file, $row['id'] . "\t" .
                     $row['name'] . "\t" .
                     $row['subject'] . "\t" .
                     $row['phone'] . "\t" .
                     $row['email'] . "\t" .
                     $message . "\n");
    }

    fclose($file);

    $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'omkarpakale@gmail.com';
            $mail->Password = 'wcpm iypo sjux qopl';     // Replace with App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('omkarpakale@gmail.com', 'Website Bot');
            $mail->addAddress('omkarpakale@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'Contact Records with Excel Attachment';
            $mail->Body = 'Hi omkieee,<br><br>Please find attached the contact records Excel file.<br><br>Regards,<br>Website Bot';

            $mail->addAttachment($filename);

            $mail->send();
            echo "<script>alert('Email sent successfully with attachment!');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Failed to send email.');</script>";
        }

    unlink($filename);
}

// Fetch records
$result = $conn->query("SELECT * FROM DEMO");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Records</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h2 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        th, td { padding: 10px; text-align: left; }
        button { padding: 10px 20px; margin-right: 10px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<h2>Contact Records</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Subject</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Message</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['subject']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['message']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found</td></tr>";
        }
        ?>
    </tbody>
</table>

<form method="POST" action="">
    <button type="submit" name="export">Download Excel</button>
    <button type="submit" name="mail">Mail Data</button>
</form>

</body>
</html>

<?php $conn->close(); ?>
