<h1>UPDATE <?php
echo $table_name; ?> - Row Id : <?php
echo $row_id; ?> </h1>
<form action="index.php" method="POST">
<input type="hidden" name="update" id="update" value="update" />
<?php
$results = $daboCRUD->getRow($table_name, $row_id);
foreach ($results as $key => $value) {
	if($key == 'id'){
		echo '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />';
	}
    // if column related to other table , 
    // replace the text input with select input
    // populated with data from related table 
    if (in_array($key, array_keys($column_table_relations))) {
        $related_column_results = $daboCRUD->getTableData($column_table_relations[$key]['table']);
        echo '<p>';
        echo '<label for=' . $key . '>' . $key . '</label>';
        echo '</p>';
        echo '<p>';
        echo '<select id="' . $key . '" name="' . $key . '" value="' . $value . '" >';
        foreach ($related_column_results as $related_column_key => $related_column_value) {
            $selected = ($related_column_value['id'] == $value) ? 'selected="selected"' : '';
           echo '<option value="' . $related_column_value['id'] . '" ' . $selected . '>' . $related_column_value[$column_table_relations[$key]['display_column']] . '</option>';
        }
        echo '</select>';
        echo '</p>';
    } 
    else {
        echo '<p>';
        echo '<label for=' . $key . '>' . $key . '</label>';
        echo '</p>';
        echo '<p>';
        echo '<input type="text" id="' . $key . '" name="' . $key . '" value="' . $value . '" />';
        echo '</p>';
    }
}
echo '<p><input type="submit"></input></p>';
echo '<a href="index.php">Back To List</a>';
?>
</form>