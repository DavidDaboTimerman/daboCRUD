<?php
echo 'Are you sure you want to delete this row ? (id = '.$row_id.')'.'<br/>';

echo '<a href="index.php?action='.urlencode('delete').'&id='.urlencode($row_id).'&sure='.urlencode(1).'">Yes</a><br/>';
echo '<a href="index.php">No</a>';
?>