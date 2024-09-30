<?php
// Include konfigurasi database
include 'configDB.php';

// Query untuk total jumlah produk
$totalProductsResult = $konek->query("SELECT COUNT(*) AS total FROM product");
$totalProducts = $totalProductsResult->fetch_assoc()['total'];

// Query untuk produk dengan stok 5
$stockFiveResult = $konek->query("SELECT COUNT(*) AS count FROM product WHERE stok <= 5");
$stockFive = $stockFiveResult->fetch_assoc()['count'];

// Query untuk produk yang stoknya habis (stok = 0)
$outOfStockResult = $konek->query("SELECT COUNT(*) AS count FROM product WHERE stok = 0");
$outOfStock = $outOfStockResult->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard with Bootstrap</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    /* Custom Styling */
    .table-container {
      margin: 20px;
    }

    /* Clock styling */
    .clock-container {
      position: relative;
      width: 240px;
      height: 240px;
      border: 8px solid black;
      border-radius: 50%;
      margin: 20px auto;
      background: white;
    }

    .clock-hand {
      position: absolute;
      bottom: 50%;
      left: 50%;
      transform-origin: bottom center;
      transform: translateX(-50%);
      background-color: black;
      border-radius: 2px;
    }

    .hour-hand {
      width: 6px;
      height: 60px;
      z-index: 3;
    }

    .minute-hand {
      width: 4px;
      height: 80px;
      z-index: 2;
    }

    .second-hand {
      width: 2px;
      height: 100px;
      background-color: red;
      z-index: 1;
    }

    .clock-center {
      position: absolute;
      width: 12px;
      height: 12px;
      background-color: black;
      border-radius: 50%;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 4;
    }

    .clock-number {
      position: absolute;
      font-size: 20px;
      transform: translate(-50%, -50%);
    }

    /* Calendar Custom Styling */
    /* Calendar Custom Styling */
.calendar-container {
  width: 100%;  /* Ensure the calendar takes full width for better alignment */
  max-width: 400px; /* Set a max width to prevent it from being too large */
  margin: 20px auto;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px; /* Increase padding for more spacing */
  background-color: #fff;
}

.calendar-header {
  text-align: center;
  font-size: 24px;
  font-weight: bold;
  padding: 10px 0;
}

.calendar-table {
  width: 100%; /* Ensure the table takes the full width of the container */
  border-collapse: collapse;
}

.calendar-table th, .calendar-table td {
  padding: 10px;
  text-align: center;
  font-size: 16px;
  border: 1px solid #ddd;
}

.calendar-table th {
  background-color: #f1f1f1;
  font-weight: bold;
}

.calendar-table td {
  height: 40px;
  vertical-align: middle; /* Vertically center the text */
}

.calendar-table td.today {
  background-color: #007bff;
  color: white;
  border-radius: 50%; /* Rounded for today’s date */
}

.arrow-button {
  cursor: pointer;
  border: none;
  background: transparent;
  font-size: 18px;
  margin: 0 10px;
}

  </style>
</head>
<body class="bg-light">
  
  <!-- Seksi Kartu Produk dengan Bootstrap -->
  <div class="container my-4">
    <div class="row text-center">
      <div class="col-md-4">
        <div class="card bg-info text-white mb-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo $totalProducts; ?></h5>
            <p class="card-text">Total Produk</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-warning text-white mb-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo $stockFive; ?></h5>
            <p class="card-text">Produk dengan Stok Dibawah 5</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-danger text-white mb-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo $outOfStock; ?></h5>
            <p class="card-text">Produk Stok Habis</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Produk -->
  <div class="container">
    <h3 class="text-center">Produk Yang Segera Di Tindak Lanjuti</h3>
    <table class="table table-bordered table-striped">
      <thead class="thead-dark">
        <tr>
          <th>Gambar</th>
          <th>Nama Produk</th>
          <th>Stok</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Menampilkan data produk dari database dengan stok di bawah 5 atau stok habis
        $productsResult = $konek->query("SELECT product_id, name_product, stok, product_image1 FROM product WHERE stok <= 5");
        while ($product = $productsResult->fetch_assoc()) {
          echo "<tr>";

          // Menampilkan gambar produk
          $imagePath = "" . $product['product_image1']; // Path ke folder uploads
          echo "<td><img src='" . $imagePath . "' alt='Gambar Produk' class='img-thumbnail' style='width: 100px; height: 100px;'></td>";

          // Menampilkan nama produk
          echo "<td>" . $product['name_product'] . "</td>";

          // Menampilkan stok produk
          echo "<td>" . $product['stok'] . "</td>";

          // Menambahkan tombol Edit
          echo "<td><button class='btn btn-primary' onclick=\"window.location.href='editProduct.php?product_id=" . $product['product_id'] . "'\">Edit</button></td>";

          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Left Panel Section with Calendar and Clock -->
  <div class="container d-flex justify-content-center my-4">
    <!-- Calendar tetap menggunakan script lama -->
    <div class="calendar-container">
    <div class="calendar-header">
      <button class="arrow-button" onclick="prevMonth()">&#8592;</button>
      <span id="monthYear"></span>
      <button class="arrow-button" onclick="nextMonth()">&#8594;</button>
    </div>

    <table class="calendar-table">
      <thead>
        <tr>
          <th>Sun</th>
          <th>Mon</th>
          <th>Tue</th>
          <th>Wed</th>
          <th>Thu</th>
          <th>Fri</th>
          <th>Sat</th>
        </tr>
      </thead>
      <tbody id="calendarBody">
        <!-- Dates will be dynamically inserted here -->
      </tbody>
    </table>
  </div>

    <!-- Clock Section -->
    <div class="clock-container">
      <div class="clock-hand hour-hand" id="hour-hand"></div>
      <div class="clock-hand minute-hand" id="minute-hand"></div>
      <div class="clock-hand second-hand" id="second-hand"></div>
      <div class="clock-center"></div>
    </div>
  </div>

  <!-- JavaScript for Clock and Calendar -->
  <script>
    // Function to display the current date
    const monthNames = [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ];

    let currentDate = new Date();

    function renderCalendar() {
      const month = currentDate.getMonth();
      const year = currentDate.getFullYear();

      document.getElementById('monthYear').textContent = `${monthNames[month]} ${year}`;

      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();

      const calendarBody = document.getElementById('calendarBody');
      calendarBody.innerHTML = '';

      let row = document.createElement('tr');
      let dayCount = 0;

      // Fill initial empty cells
      for (let i = 0; i < firstDay; i++) {
        row.appendChild(document.createElement('td'));
        dayCount++;
      }

      // Fill the days
      for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('td');
        cell.textContent = day;

        // Highlight today's date
        const today = new Date();
        if (
          day === today.getDate() &&
          month === today.getMonth() &&
          year === today.getFullYear()
        ) {
          cell.classList.add('today');
        }

        row.appendChild(cell);
        dayCount++;

        // Start a new row if we've reached the end of the week
        if (dayCount % 7 === 0) {
          calendarBody.appendChild(row);
          row = document.createElement('tr');
        }
      }

      // Fill the last row with empty cells (if needed)
      while (dayCount % 7 !== 0) {
        row.appendChild(document.createElement('td'));
        dayCount++;
      }

      calendarBody.appendChild(row);
    }

    function prevMonth() {
      currentDate.setMonth(currentDate.getMonth() - 1);
      renderCalendar();
    }

    function nextMonth() {
      currentDate.setMonth(currentDate.getMonth() + 1);
      renderCalendar();
    }


    // Function to update the clock
    function updateClock() {
      const now = new Date();

      const secondHand = document.getElementById('second-hand');
      const minuteHand = document.getElementById('minute-hand');
      const hourHand = document.getElementById('hour-hand');

      const seconds = now.getSeconds();
      const minutes = now.getMinutes();
      const hours = now.getHours();

      const secondDegree = (seconds / 60) * 360;
      const minuteDegree = (minutes / 60) * 360 + (seconds / 60) * 6;
      const hourDegree = (hours % 12 / 12) * 360 + (minutes / 60) * 30;

      secondHand.style.transform = `translateX(-50%) rotate(${secondDegree}deg)`;
      minuteHand.style.transform = `translateX(-50%) rotate(${minuteDegree}deg)`;
      hourHand.style.transform = `translateX(-50%) rotate(${hourDegree}deg)`;
    }

    // Function to place numbers in a perfect circle
    function placeClockNumbers() {
      const clockContainer = document.querySelector('.clock-container');
      const radius = 100;
      const centerX = clockContainer.offsetWidth / 2;
      const centerY = clockContainer.offsetHeight / 2;

      for (let i = 1; i <= 12; i++) {
        const angle = (i * 30) * (Math.PI / 180);
        const x = centerX + radius * Math.sin(angle);
        const y = centerY - radius * Math.cos(angle);

        const numberElement = document.createElement('div');
        numberElement.classList.add('clock-number');
        numberElement.style.left = `${x}px`;
        numberElement.style.top = `${y}px`;
        numberElement.textContent = i;
        clockContainer.appendChild(numberElement);
      }
    }

    setInterval(updateClock, 1000);
    updateClock();

    renderCalendar();

    window.onload = placeClockNumbers;
  </script>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
