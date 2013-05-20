<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();

$subject_id = $_GET['student'];
$pre_col = $_GET['pre_col'];
$post_col = $_GET['post_col'];

$db->db_delete_bev_grading_submission($subject_id, $pre_col, $post_col);

print '<head>
<script>
window.location.replace("./bev_grade_pre_post_questions.php?id='. $pre_col .'&student='. $subject_id.'");
</script>
</head>';


?>