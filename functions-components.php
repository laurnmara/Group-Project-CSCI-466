<html>
<?php

    // Username and Password for connecting to DB
    $username='z2020678';
    $password='2005Oct28';

    // Function for drawing tables
    function draw_table($rows) {
        if (!$rows) { echo 'No results found!'; die(); }
        else {
            echo "<table border=1 cellspacing=2>";
                echo "<tr>";
                foreach($rows[0] as $key => $item ) {
                    echo "<th>$key</th>";
                }
                echo "</tr>";
                foreach($rows as $row) {
                    echo "<tr>";
                    foreach($row as $item) {
                        echo "<td>$item</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
        }
    }

    //Nav Bar component


    //Footer component
?>
</html>
