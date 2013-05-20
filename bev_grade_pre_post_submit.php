<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();



/*
Data arrives in the $_post array and looks like this:

Array
(
    [subject_id] => 180
    [score,what_record_of_planning,if_so_how_change] => -2
    [confidence,what_record_of_planning,if_so_how_change] => 0
    [comments,what_record_of_planning,if_so_how_change] => 
    [score,what_record_of_planning,what_record_of_planning] => -2
    [confidence,what_record_of_planning,what_record_of_planning] => 0
    [comments,what_record_of_planning,what_record_of_planning] => 
)

So - the first thing is just the student id (pre test id)

The other contents come in triplets - 3 bits of data for each row.
And the 'key' contains data also - split by comma.  
	Score or confidence level or comments.
	Then the pre-column, then the post column
		E.g. Score for "what record of planning" pre col and "if so how change" post col for student 180 would be -2.
*/
$subject_id = NULL;
$item = array();
$pre_col = NULL;
// this mungs the data into an acceptable and easy to work with format.
foreach ($_POST as $key=>$var) {
	if ($key=="subject_id") {
		$subject_id = $var;
	}
	else {
		$information = explode(",", $key);
		$item[$subject_id] [$information[1]] [$information[2]] [$information[0]] = $var;
		$pre_col = $information[1];
	}
}

$db->db_save_bev_grading_submission($item);

print '<head>
<script>
window.location.replace("./bev_grade_pre_post_questions.php?id='. $pre_col .'&student='. $subject_id.'");
</script>
</head>';


?>