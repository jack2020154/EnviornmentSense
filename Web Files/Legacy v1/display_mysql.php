<html><head><title>MySQL Table Viewer</title></head><body>
<?php
date_default_timezone_set('America/Toronto');
$db_host = '127.0.0.1';
$db_user = 'web_viewer';
$db_pwd = '923hfjd83!';
$database = 'sensor_db';
$table = 'sensor_data';
$timestamp = date("Y-m-d-H-i-s");
$svrstatus = 'SQL Server is online';
$dbstatus = 'Database is online';
@$link = mysqli_connect($db_host, $db_user, $db_pwd, $database);


if (!$link)
    echo '<h1><span style ="color:#FF0000;">SERVER OFFLINE</span></h1>';
else if (@!mysqli_select_db($link, $database))
    echo '<h1><span style ="color:#FFFF00;">DATABASE OFFLINE</span></h1>';
else if (@!mysqli_query($link, "SELECT * FROM {$table}"))
    echo '<h1><span style ="color:#FFFF00;">TABLE OFFLINE</span></h1>';
else {
    echo '<h1><span style ="color:#00FF00;">ONLINE</span></h1>';
}
    



// sending query
@$result = mysqli_query($link, "SELECT * FROM {$table}");


@$fields_num = mysqli_num_fields($result);
echo ("<a href=index.html> Return home </a>");
echo "<link rel='stylesheet' type='text/css'  href='style.css'>";
echo "<h1>SQL Server IP: {$db_host}</h1>";
echo "<h1>Database: {$database}</h1>";
echo "<h1>Table: {$table}</h1>";
echo "<h1>Last update time: {$timestamp} </h1>";

echo "<table border='1'><tr>";
// printing table headers
for($i=0; $i<$fields_num; $i++)
{
    $field = mysqli_fetch_field($result);
    echo "<td>{$field->name}</td>";
}
echo "</tr>\n";
// printing table rows
while(@$row = mysqli_fetch_row($result))
{
    echo "<tr>";

    // $row is array... foreach( .. ) puts every element
    // of $row to $cell variable
    foreach($row as $cell)
        echo "<td>$cell</td>";

    echo "</tr>\n";
}
@mysqli_free_result($result);
?>
</body></html>

<!-- http://www.anyexample.com/programming/php/php_mysql_example__display_table_as_html.xml -->