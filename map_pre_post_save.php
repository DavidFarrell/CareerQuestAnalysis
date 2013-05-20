<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();


$db->map_pre_post_questions($_POST);

print "<head>
<script>
window.setInterval(function() { window.history.back(); }  ,1000);
</script>
</head>";


?>