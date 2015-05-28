<?php
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username == 'yanir' && $password == 'yanivvenir') {
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
    }
}

if ((!isset($_SESSION['username']) || !isset($_SESSION['username'])) || ($_SESSION['username'] != 'yanir' || $_SESSION['password'] != 'yanivvenir')) {
    include 'login.php';
    die;
}

echo '<a href="index.php?action=add">Add Row</a>';
echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="crudTable">';
echo '<thead>';
echo '<tr>';
foreach ($table_column_names as $column_name) {
    echo '<th>' . $column_name . '</th>';
}
echo '<th>Actions</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
foreach ($results as $row) {
    echo '<tr>';
    foreach ($row as $key => $value) {
        
        // if column related to other table ,
        // replace the cell with corresponding
        // descriptive name from related table
        if (in_array($key, array_keys($column_table_relations)) == true && $value) {
            $related_column_results = $daboCRUD->getRow($column_table_relations[$key]['table'], $value);
            echo '<td>' . $related_column_results[$column_table_relations[$key]['display_column']] . '</td>';
        } 
        else {
            echo '<td>' . $value . '</td>';
        }
    }
    
    echo '<td>' . '<a href="index.php?action=edit&id=' . $row['id'] . '">Update Row</a> | <a href="index.php?action=delete&id=' . $row['id'] . '">Delete Row</a> | <a href="index.php?action=attach&id=' . $row['id'] . '">Site To Admin</a>' . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>