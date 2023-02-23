<?php
require_once('database/conn.php');

$animeTitle = "Gintama";

$stmt = $db -> prepare("SELECT * FROM animes_test WHERE title = ?");
$stmt -> bind_param('s', $animeTitle);
$stmt -> execute();
// get_result() no funciona con $stmt porque pertenece a la clase mysqli_result y no a mysqli_stmt.
$result = $stmt -> get_result();

if ($result -> num_rows > 0) {
    while ($row = $result -> fetch_assoc()) {

    }
}



?>


<section class="ikanaide-body-left-column box">

</section>
<section class="ikanaide-body-right-column box">

</section>