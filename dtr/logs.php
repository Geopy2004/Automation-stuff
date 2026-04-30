<?php include("../auth/session.php"); ?>
<?php include("../config/db.php"); ?>

<!DOCTYPE html>
<html>
<head>
  <title>DTR Logs</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="layout">

  <div class="sidebar">
    <a href="../dashboard/index.php">Dashboard</a>
    <a href="logs.php">Logs</a>
    <a href="../auth/logout.php">Logout</a>
  </div>

  <div class="main">
    <h2>Daily Time Record</h2>

    <a href="time_in.php"><button>Time In</button></a>
    <a href="time_out.php"><button>Time Out</button></a>

    <table border="1" width="100%">
      <tr>
        <th>Type</th>
        <th>Time</th>
      </tr>

      <?php
      $uid = $_SESSION['user_id'];
      $logs = $conn->query("SELECT * FROM logs WHERE user_id=$uid ORDER BY id DESC");

      while ($row = $logs->fetch_assoc()) {
        echo "<tr>
                <td>{$row['type']}</td>
                <td>{$row['time']}</td>
              </tr>";
      }
      ?>
    </table>

  </div>

</div>

</body>
</html>