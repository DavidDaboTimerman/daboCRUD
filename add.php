<h1>Add Row <?php echo $table_name; ?> - Row Id : <?php echo $row_id; ?> </h1>
<form action="index.php" method="POST">
<input type="hidden" name="add" id="add" value="add" />
<?php 
foreach($table_column_names as $key => $value){
	if($value == 'id'){
		echo '<input type="hidden" id="id" value="NULL" />';  
        continue;
	}
    // if column related to other table , 
    // replace the text input with select input
    // populated with data from related table 
	if (in_array($value, array_keys($column_table_relations))) {
        $related_column_results = $daboCRUD->getTableData($column_table_relations[$value]['table']);
        echo '<p>';
        echo '<label for=' . $value . '>' . $value . '</label>';
        echo '</p>';
        echo '<p>';
        echo '<select id="' . $value . '" name="' . $value . '" value="' . $value . '" >';
        foreach ($related_column_results as $related_column_key => $related_column_value) {
            echo '<option value="' . $related_column_value['id'] . '">' . $related_column_value[$column_table_relations[$value]['display_column']] . '</option>';
        }
        echo '</select>';
        echo '</p>';
    } 
    else {
        echo '<p>';
        echo '<label for=' . $value . '>' . $value . '</label>';
        echo '</p>';
        echo '<p>';
        echo '<input type="text" id="' . $value . '" name="' . $value . '" value="' . $value . '" />';
        echo '</p>';
    }


}
echo '<p><input type="submit"></input></p>';
echo '<a href="index.php">Back To List</a>';
?>
</form>