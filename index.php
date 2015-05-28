<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>

</head>
<body>
<?php
if (isset($_GET['action']) && $_GET['action'] == 'refresh') {
    header('Location:index.php');
} 
else {
    header('Content-Type: text/html; charset=utf-8');
}

require_once ('../cp/sources/config.inc.php');
require_once ('crud.php');

$daboCRUD = new Crud(DB_SERVER, DB_DATABASE, DB_USER, DB_PASS);
$table_name = 'sites_modules_users_users';

// check for new or updated data
if (isset($_POST['add'])) {
    $value_array = $_POST;
    unset($value_array['add']);
    $value_array['password'] = md5(KEY_START . $value_array['password'] . KEY_END);
    
    // ENCRYPT PASSWORD BEFORE SAVING
    echo $daboCRUD->insertRow($table_name, $value_array);
} 
elseif (isset($_POST['update'])) {
    $value_array = $_POST;
    $id = $_POST['id'];
    unset($value_array['update']);
    unset($value_array['id']);
    
    $oldRow = $daboCRUD->getRow($table_name, $id);
    unset($oldRow['id']);
    
    $diff_array = array_diff($value_array, $oldRow);
    
    if (isset($value_array['isOwner']) && $value_array['isOwner'] != $oldRow['isOwner']) {
        $diff_array['isOwner'] = $value_array['isOwner'];
    }
    
    if (!empty($diff_array)) {
        if (isset($diff_array['password'])) {
            $diff_array['password'] = md5(KEY_START . $diff_array['password'] . KEY_END);
        }
        echo ($daboCRUD->updateRow($table_name, $diff_array, $id)) ? 'Update Successful' : 'Nothing Was Changed';
    } 
    else {
        echo 'nothing changed';
    }
} 
elseif (isset($_POST['attach'])) {
    $value_array = $_POST;
    $id = $_POST['id'];
    unset($value_array['attach']);
    unset($value_array['id']);
    unset($value_array['all']);
    $siteString = implode(',', $value_array['sites']);
    $sites_to_admin_row = $daboCRUD->getRow('sites_sites_to_admin', $id, 'admin_user_id');
    if (!$sites_to_admin_row) {
        $insert_data = array('id' => NULL, 'admin_user_id' => $id, 'sites_string' => $siteString);
        $insert_status = $daboCRUD->insertRow('sites_sites_to_admin', $insert_data);
        header('Location:index.php');
    } 
    else {
        $update_data = array('sites_string' => $siteString);
        $update_status = $daboCRUD->updateRow('sites_sites_to_admin', $update_data, $id, 'admin_user_id');
        header('Location:index.php');
    }
}

// set relations between columns and other tables
$column_table_relations = array('siteId' => array('table' => 'sites_sites', 'display_column' => 'siteName'), 'groupId' => array('table' => 'sites_modules_users_groups', 'display_column' => 'name'),);
$results = $daboCRUD->getTableData($table_name);
$table_column_names = $daboCRUD->getTableColumnNames($table_name);
?>

<h1>Admin Management - Banner.co.il</h1>

<?php

// get action from url and load view accordingly
// get id for the views that need it

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;
$row_id = (isset($_GET['id'])) ? $_GET['id'] : '';
$confirm = (isset($_GET['sure'])) ? $_GET['sure'] : FALSE;

switch ($action) {
    case 'add':
        include ('add.php');
        break;

    case 'edit':
        include ('edit.php');
        break;

    case 'delete':
        if ($confirm == FALSE) {
            include ('delete.php');
        } 
        else {
            $daboCRUD->deleteRow($table_name, $row_id);
            echo $row_id . ' has been deleted.';
            echo '<a href="index.php?action=refresh">Back To List</a>';
        }
        break;

    case 'attach':
        $sites_attached_to_user = $daboCRUD->getRow('sites_sites_to_admin', $row_id, 'admin_user_id');
        include ('siteToAdmin.php');
        break;

    default:
        
        include ('list.php');
        break;
}
?>
<script type="text/javascript">
    $(document).ready(function(){
    $('#crudTable').DataTable({
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        columnDefs: [
            { width: '20%', targets: 0 }
        ]
    });
});

</script>
</body>
</html>