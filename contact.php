<?php
$page_title = "Contact Us | SellMyPhone UAE";
$meta_description = "Get in touch with SellMyPhone for questions about selling your phone in UAE. Reach us via WhatsApp, phone, or our contact form.";
$meta_keywords = "contact sellmyphone, sellmyphone UAE support, sell phone help";
session_start();
require_once 'admin/includes/db.php';
/**@var mysqli $conn */

$contact_msg = "";
if (isset($_SESSION['contact_msg'])) {
    $contact_msg = $_SESSION['contact_msg'];
    unset($_SESSION['contact_msg']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $subject   = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message   = mysqli_real_escape_string($conn, trim($_POST['message']));

    $sql = "INSERT INTO contact_messages (full_name, email, phone, subject, message)
            VALUES ('$full_name', '$email', '$phone', '$subject', '$message')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['contact_msg'] = "<div class='alert alert-success'>Message sent successfully! We'll contact you.</div>";
    } else {
        $_SESSION['contact_msg'] = "<div class='alert alert-danger'>Error occurred, try again.</div>";
    }

    header("Location: contact.php");
    exit();
}

// ---------------- Dynamic contact info ----------------
$info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM contact_info WHERE id=1"));

require 'includes/header.php';
?>
<section class="contact">
    <div class="container ">
         <div class="row overflow-hidden border rounded-4 shadow-sm">

            <div class="col-lg-6 col-md-12 contact-info px-5">
                <div class="contact-content mt-5 text-center">
                    <h3 class=" fw-bold fs-1 text-start">Get In Touch With Us</h3>
                    <p class="text-white text-start">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                </div>
                 <div>

                    <div class="d-flex align-items-center mb-3">
                      <div class="icon-box2 d-flex align-items-center justify-content-center fs-5 m-3"><i class="fa-solid fa-phone-flip text-white"></i></div>
                      <div class="py-2"> <h3 class="fs-5 fw-bold text-white">Call us</h3>
                         <a href="tel:<?php echo htmlspecialchars($info['phone']); ?>" class="text-secondary text-decoration-none text-white">
                         <?php echo htmlspecialchars($info['phone']); ?>
                        </a>
                      </div>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                     <div class="icon-box2 d-flex align-items-center justify-content-center fs-5 m-3"><i class="fa-solid fa-envelope"></i></div>
                      <div class="py-2"> <h3 class="fs-5 fw-bold text-white">Email us</h3>
                         <a href="mailto:<?php echo htmlspecialchars($info['email']); ?>" class="text-secondary text-decoration-none text-white">
                            <?php echo htmlspecialchars($info['email']); ?>
                        </a></div>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                      <div class="icon-box2 d-flex align-items-center justify-content-center fs-5 m-3"><i class="fa-solid fa-location-dot text-white"></i></div>
                      <div class="py-2"><h3 class="fs-5 fw-bold text-white">Visit us</h3>  
                        <p class="text-white mb-0"><?php echo htmlspecialchars($info['address']); ?></p>
                       </div>
                    </div>

                </div>
                <div class="d-flex-column">
                    <div class="mt-4">
                        <h3 class="fs-5 fw-bold text-white">Follow us</h3>
                    </div>
                    <div class="d-flex gap-4 mt-3 mb-3">
                        <div class="d-flex justify-content-center align-items-center mt-2 icon-box1">
                            <a href="<?php echo htmlspecialchars($info['facebook_url']); ?>" class="text-white fs-5">
                                <i class="fa-brands fa-facebook"></i>
                            </a>
                        </div>
                        <div class="d-flex justify-content-center align-items-center mt-2 icon-box1">
                            <a href="<?php echo htmlspecialchars($info['instagram_url']); ?>" class="text-white fs-5">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        </div>
                        <div class="d-flex justify-content-center align-items-center mt-2 icon-box1">
                            <a href="<?php echo htmlspecialchars($info['linkedin_url']); ?>" class="text-white fs-5">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </div>
                        <div class="d-flex justify-content-center align-items-center mt-2 icon-box1">
                            <a href="<?php echo htmlspecialchars($info['twitter_url']); ?>" class="text-white fs-5">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 mt-5 sendmsg">Send Us a Message</h2>
                <p class="text-secondary mb-5">Fill out the form below and we'll get back to you within 24 hours.</p>
                <form method="POST" action="">
                    <?php echo $contact_msg; ?>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-user me-2"></i>Full Name</label>
                            <input type="text" name="full_name" class="form-control custom-input" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-envelope me-2"></i>Email Address</label>
                            <input type="email" name="email" class="form-control custom-input" placeholder="john@example.com" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-phone me-2"></i>Phone Number</label>
                            <input type="tel" name="phone" class="form-control custom-input" placeholder="+971 50 555 6779">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-tag me-2"></i>Subject</label>
                            <input type="text" name="subject" class="form-control custom-input" placeholder="How can we help?">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-comment-dots me-2"></i>Your Message</label>
                            <textarea name="message" class="form-control custom-input" rows="6" placeholder="Tell us more about your inquiry..." required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="send_message" class="btn custom-btn w-100">
                                Send Message <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>
<?php require 'includes/footer.php';?>