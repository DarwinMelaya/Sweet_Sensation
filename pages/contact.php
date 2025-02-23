<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Sensations - Contact</title>
    <!-- CSS FILES -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product.css">
    <!-- BOX ICON LINKS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/Sweet Sensations/components/header.php'; ?>

    <!-- CONTACT SECTION -->
    <section class="contact" id="contact">
        <div class="contact-container">
            <form action="https://api.web3forms.com/submit" method="POST" class="contact-left">
                <div class="contact-left-title">
                    <h2>Get in Touch</h2>
                    <p>We'd love to hear from you! Send us a message.</p>
                    <hr />
                </div>
                <input type="hidden" name="access_key" value="ba5b4ac5-1019-483b-86f6-cd701e940552" />
                <input type="text" name="name" placeholder="Your Name" class="contact-inputs" required />
                <input type="email" name="email" placeholder="Your Email" class="contact-inputs" required />
                <textarea name="message" placeholder="Your Message" class="contact-inputs" rows="6" required></textarea>
                <button type="submit" class="submit-btn">
                    Send Message <i class='bx bx-send'></i>
                </button>
            </form>
            <div class="contact-right">
                <div class="contact-info">
                    <h3>Contact Information</h3>
                    <div class="info-item">
                        <i class='bx bx-map'></i>
                        <p>123 Sweet Street, Bakery Lane<br>Dessert City, DC 12345</p>
                    </div>
                    <div class="info-item">
                        <i class='bx bx-phone'></i>
                        <p>(555) 123-4567</p>
                    </div>
                    <div class="info-item">
                        <i class='bx bx-envelope'></i>
                        <p>delrosariomarwin06@gmail.com</p>
                    </div>
                    <div class="social-links">
                        <a href="#"><i class='bx bxl-facebook'></i></a>
                        <a href="#"><i class='bx bxl-instagram'></i></a>
                        <a href="#"><i class='bx bxl-twitter'></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .contact {
            padding: 80px 20px;
            background-color: #f9f9f9;
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .contact-left-title {
            margin-bottom: 30px;
        }

        .contact-left-title h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
        }

        .contact-left-title p {
            color: #666;
            margin-bottom: 20px;
        }

        .contact-left-title hr {
            width: 60px;
            height: 3px;
            background: #ff6b6b;
            border: none;
            margin: 0;
        }

        .contact-inputs {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .contact-inputs:focus {
            border-color: #ff6b6b;
            outline: none;
        }

        .submit-btn {
            background: #ff6b6b;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #ff5252;
        }

        .contact-info {
            padding: 20px;
        }

        .contact-info h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 25px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item i {
            font-size: 1.5rem;
            color: #ff6b6b;
        }

        .info-item p {
            color: #666;
            line-height: 1.6;
        }

        .social-links {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }

        .social-links a {
            color: #666;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: #ff6b6b;
        }

        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .contact {
                padding: 40px 15px;
            }

            .contact-left-title h2 {
                font-size: 2rem;
            }
        }
    </style>

</body>

</html>