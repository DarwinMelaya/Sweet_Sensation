<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sweet Sensations</title>
  <!-- CSS FILE -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/product.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <!-- BOX ICON LINKS -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }

    .hero {
      position: relative;
      padding: 100px 0;
      text-align: center;
      color: white;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
      overflow: hidden;
    }

    .hero-slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      opacity: 0;
      transition: opacity 1s ease-in-out;
    }

    .hero-slide.active {
      opacity: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
    }

    .hero h1 {
      font-size: 2.5em;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 1.2em;
      margin-bottom: 30px;
    }

    .featured-products {
      padding: 50px 0;
    }

    .featured-products h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .view-more {
      text-align: center;
      margin-top: 30px;
    }

    .btn {
      background: #ff6b6b;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .btn:hover {
      background: #ff5252;
    }
  </style>

</head>

<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/Sweet Sensations/components/header.php'; ?>

  <section class="hero">
    <div class="hero-slide" style="background-image: url('https://i.pinimg.com/736x/30/f1/48/30f1487e4d2f3bb76065315210311adf.jpg');"></div>
    <div class="hero-slide" style="background-image: url('https://t4.ftcdn.net/jpg/09/06/19/95/360_F_906199553_bgMA47aduJPKTPuUG5T7ePk706a07OkU.jpg');"></div>
    <div class="hero-slide" style="background-image: url('https://static.vecteezy.com/system/resources/previews/032/020/082/large_2x/baking-background-a-variety-of-ingredients-for-baking-on-rustic-background-photo.jpg');"></div>
    <div class="hero-content">
      <h1>Welcome to Sweet Sensations</h1>
      <p>Discover our delightful collection of cakes and pastries</p>
    </div>
  </section>

  <section class="featured-products">
    <div class="container">
      <h2>Our Best Sellers</h2>
      <div class="product-container">
        <?php
        $conn = new mysqli("localhost", "root", "", "sweet_sensations");
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 3";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()): ?>
          <div class="product">
            <div class="image">
              <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            </div>
            <div class="pduct-info">
              <h4><?php echo $row['name']; ?></h4>
              <p class="product-price">₱<?php echo $row['price']; ?></p>
            </div>
          </div>
        <?php endwhile;
        $conn->close();
        ?>
      </div>
      <div class="view-more">
        <a href="pages/product.php" class="btn">View All Products</a>
      </div>
    </div>
  </section>

  <script>
    // Add carousel functionality
    const slides = document.querySelectorAll('.hero-slide');
    let currentSlide = 0;

    function showSlide(index) {
      slides.forEach(slide => slide.classList.remove('active'));
      slides[index].classList.add('active');
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % slides.length;
      showSlide(currentSlide);
    }

    // Show first slide and start carousel
    showSlide(0);
    setInterval(nextSlide, 3000); // Change slide every 3 seconds
  </script>

</body>

</html>