<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();


$db->map_students($_POST);

print "<head>
<script>
window.setInterval(function() { window.history.back(); }  ,1000);
</script>
</head>";


?>