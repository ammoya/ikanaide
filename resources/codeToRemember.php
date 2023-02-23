<?php

// SELECT through parameters.
$animeTitle = "Gintama"; // This would be $_POST[]

$stmt = $db -> prepare("SELECT * FROM animes_test WHERE title = ?");
$stmt -> bind_param('ss', $animeTitle);
$stmt -> execute();
// get_result() no funciona con $stmt porque pertenece a la clase mysqli_result y no a mysqli_stmt.
$result = $stmt -> get_result();

if ($result -> num_rows > 0) {
    while ($row = $result -> fetch_assoc()) {
        $animeTitle = $row['title'];
    }
}