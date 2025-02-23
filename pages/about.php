<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Sensations - About</title>
    <!-- CSS FILE -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product.css">
    <!-- BOX ICON LINKS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .about-hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('https://i.pinimg.com/736x/30/f1/48/30f1487e4d2f3bb76065315210311adf.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 0;
            margin-bottom: 50px;
        }

        .about-section {
            padding: 50px 0;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 50px;
        }

        .feature-box {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .feature-box i {
            font-size: 40px;
            color: #ff6b6b;
            margin-bottom: 15px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        p {
            line-height: 1.6;
            color: #666;
        }
    </style>
</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/Sweet Sensations/components/header.php'; ?>

    <section class="about-hero">
        <div class="container">
            <h1>About Sweet Sensations</h1>
            <p>Creating Sweet Memories Since 2020</p>
        </div>
    </section>

    <section class="about-section">
        <div class="about-content">
            <h2>Our Story</h2>
            <p>Sweet Sensations began with a simple passion for creating delightful treats that bring joy to people's lives. What started as a small home bakery has grown into a beloved destination for cake lovers and dessert enthusiasts.</p>

            <div class="about-grid">
                <div class="feature-box">
                    <i class='bx bx-cake'></i>
                    <h3>Quality Ingredients</h3>
                    <p>We use only the finest ingredients to ensure every bite is perfect.</p>
                </div>
                <div class="feature-box">
                    <i class='bx bx-heart'></i>
                    <h3>Made with Love</h3>
                    <p>Each creation is crafted with care and attention to detail.</p>
                </div>
                <div class="feature-box">
                    <i class='bx bx-medal'></i>
                    <h3>Expert Bakers</h3>
                    <p>Our team of skilled bakers brings years of experience.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p><small>All rights reserved by Sweet Sensations &copy; 2025</small></p>
        </div>
    </footer>
</body>

</html>