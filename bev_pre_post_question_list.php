<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();


$questions = $db->db_get_bev_pre_post_question_list();

?>
<h1>List of Questions to be Graded</h1>
<ol>
	<?php
	foreach ($questions as $index=>$text) {?>
	<li><a href="bev_grade_pre_post_questions.php?id=<?php print $index;?>"><?php print $text;?></a></li>	
	<?php
	}
	?>

</ol>