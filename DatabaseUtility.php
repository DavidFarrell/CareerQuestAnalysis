<?php

require_once ("./database_config.php");
class DatabaseUtility {
	public $db;

	function __construct() {
		$this->db_connect();
		
	}
	
	function __toString() {
		return "Connection status: " . $this->db;	
	}

	function db_connect() {
	
		$hostname = $GLOBALS['MYSQL_HOST'];
		$port = $GLOBALS['MYSQL_PORT'];
		$username= $GLOBALS['MYSQL_USER'];
		$password = $GLOBALS['MYSQL_PASSWORD'];
		$database = $GLOBALS['MYSQL_DATABASE'];

//$link = mysql_connect($hostname,$username,$password);
//mysql_select_db($database) or die("Unable to select database");
		$this->db = mysql_connect($hostname,$username,$password);
		//mysql_connect($this->MYSQL_HOST, $this->MYSQL_USER, $this->MYSQL_PASSWORD);
		
		if (!$this->db) {
			die ('Could not connect to database host: ' . mysql_error());
		} else {
			$db_selected = mysql_select_db($database, $this->db);
			if (!$db_selected) {
				die ("Can't connect to database: " . mysql_error());
			} else {
				//$this->db_log("OpenConnection", "".$this->db);	
			}
		}
	}
	
	function db_disconnect(){
		$this->db_log("CloseConnection", "".$this->db);
		mysql_close($this->db);	
	}
	
	// returns array
	function db_get_full_text() {
		if(!$this->db) {
			db_connect();
		}
		
		$questions = array();
		
		$sql = "SELECT * FROM full_text_questions order by question_id asc";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)) {
		  $questions[$row['question_id']] = $row['full_text']; 
		} 
		
		return $questions;
	}
	
	// returns array
	function db_get_pre_post_pairing() {
		if(!$this->db) {
			db_connect();
		}
		
		$questions = array();
		
		$sql = "SELECT * FROM pre_post_pairing order by pre_id desc, post_id asc";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)) {
		  $questions[$row['pre_id']]['pre_col'] = $row['pre_col']; 
		  $questions[$row['pre_id']]['post_col'] = $row['post_col']; 
		} 
		
		return $questions;
	}
	
	function db_get_bev_pre_post_question_list() {
		
		if(!$this->db) {
			db_connect();
		}
		
		$questions = array();
		
		$sql = "SELECT pre_id, pre_col, full_text FROM pre_post_pairing, full_text_questions where pre_id = question_id group by pre_col order by pre_id asc";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)) {
		  $questions[$row['pre_col']]= $row['full_text']; 
		} 
		
		return $questions;		
	}
	
	function db_get_bev_question_for_grading($id) {
		if(!$this->db) {
			db_connect();
		}
		$id = $this->db_escape($id);
		$questions = array();
		
		// this bit just gets the full text used in each version of the Q 
		// and also the pre and post ids (used to refer to the mapping table) 
		// and columns (used to find the right bit in student answers).
		$sql = "SELECT pre_id, post_id, pre_col, post_col, pre_questions.full_text as pre_text, post_questions.full_text as post_text FROM `pre_post_pairing`, full_text_questions as pre_questions, full_text_questions as post_questions where pre_id = pre_questions.question_id and post_id = post_questions.question_id
and pre_col = '".$id."'
 ORDER BY `pre_id` DESC, `post_id` DESC";
		
//print $sql . "\n";
		
		// there can be more than one question (e.g. 3 post questions for one pre question)
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)) {
		  $questions[] =  $row;  
		} 
		
		// now we want to loop around and for each question get the pre and post data from each student
		foreach ($questions as $index=>$question) {
			
			// first the pre questions
			$sql = "SELECT subject_id, ".$question['pre_col']." FROM `pre_questions` order by subject_id asc";	
			$result = mysql_query($sql);
			while($row = mysql_fetch_array($result)) {
			  $questions[$index]['pre_answers'][$row['subject_id']] =  $row[$question['pre_col'] ];  
			} 
			
			// then the post questions
			$sql = "SELECT student_id, ".$question['post_col']." FROM `post_questions` order by student_id asc";	
			$result = mysql_query($sql);
			while($row = mysql_fetch_array($result)) {
			  $questions[$index]['post_answers'][$row['student_id']] =  $row[$question['post_col'] ];  
			} 
		}
		
		// by the end of this the array looks like this:
		/*
			[index by nothing]->array for the question
			ArrayForTheQuestion { [pre_id], [post_id], [pre_col], [post_col], [pre_text], [post_text], [pre_answers](array), [post_answers](array) }
			
				The [pre_answers] array is indexed by the student id in the pre_questions table
				The [post_answers] array is indexed by the student id in the post_questions table
		*/
		
		//print_r($questions);
		
	//	die();
		
		
		
		return array_reverse($questions, true);			
	}
	
	function db_get_bev_question_grading_submissions($id, $subject_id) {
		
	
		if(!$this->db) {
			db_connect();
		}
		$id = $this->db_escape($id);
		$questions = array();
		
		
		$sql = "select * from pre_post_bev_analysis where subject_id = " . $subject_id . " and pre_col = '".$id."'";
		// there can be more than one question (e.g. 3 post questions for one pre question)
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)) {
			$bev_submissions[$row['post_col']]['score'] = $row['score'];
			$bev_submissions[$row['post_col']]['confidence'] = $row['confidence'];
			$bev_submissions[$row['post_col']]['comments'] = $row['comments'];
		} 
		
		if (sizeof($bev_submissions) < 1) {
			$bev_submissions = NULL;
		} 
		return $bev_submissions;			
	}
	
	/*
		Data comes in like this:
			Array
(	
    [subject_id] => Array
        (
            [pre_column] => Array
                (
                    [post_column] => Array
                        (
                            [score] => -2
                            [confidence] => 0
                            [comments] => a
                        )

                    [post_column] => Array
                        (
                            [score] => -1
                            [confidence] => 1
                            [comments] => s
                        )

                    [post_column] => Array
                        (
                            [score] => 0
                            [confidence] => 2
                            [comments] => d
                        )

                )

        )

)
*/
	function db_save_bev_grading_submission($bev_answers){
		if(!$this->db) {
			db_connect();
		}
		
		$items = array();
		foreach($bev_answers as $subject_id => $answers) {
			foreach($answers as $pre_col=>$posts) {
				foreach($posts as $post_col=>$ratings) {
					$item_to_save = array();
					$item_to_save["subject_id"] = $subject_id;
					$item_to_save["pre_col"] = $this->db_escape($pre_col);
					$item_to_save["post_col"] = $this->db_escape($post_col);
					
					foreach($ratings as $rating_type=>$bev_rating) {
						$item_to_save[$rating_type] = $this->db_escape($bev_rating);
					}
					$items[] = $item_to_save;
				}
			}
		}
		foreach($items as $key=>$item) {
			$sql = "REPLACE INTO  `pre_post_bev_analysis` (
					`subject_id` ,
					`pre_col` ,
					`post_col` ,
					`score`, `confidence`, `comments` 
					)
					VALUES (".$item["subject_id"].", '".$item["pre_col"]."', '".$item["post_col"]."', ".$item["score"].", ".$item["confidence"].", '".$item["comments"]."' )";
			//print $sql;
			$result = mysql_query($sql);
			if (!$result) {
				print "Bev, Please copy all of the text on this page and email to david farrell, thanks!";
				die(mysql_error());	
			}
		}
	}
	
	function db_delete_bev_grading_submission($subject_id, $pre_col, $post_col) {	
	
		if(!$this->db) {
			db_connect();
		}
		$subject_id = $this->db_escape($subject_id);
		$pre_col 	= $this->db_escape($pre_col);
		$post_col 	= $this->db_escape($post_col);
		
		
		$sql = "delete from pre_post_bev_analysis where subject_id = " . $subject_id . " and pre_col = '".$pre_col."' and post_col = '".$post_col."'";
		
		$result = mysql_query($sql);
		
	}
	
	// returns array
	function db_get_pre_questions() {
		if(!$this->db) {
			db_connect();
		}
		
		$questions = array();
		
		$sql = "Show columns from pre_questions";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
		  $questions[] = $row['Field']; 
		} 
		return $questions;
	}
	// returns array
	function db_get_post_questions() {
		if(!$this->db) {
			db_connect();
		}
		
		$questions = array();
		
		$sql = "Show columns from post_questions";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
		  $questions[] = $row['Field']; 
		} 
		return $questions;
	}
	
	function map_pre_post_questions($prepost) {
		if(!$this->db) {
			db_connect();
		}
		
		$sql = "INSERT INTO  `pre_post_pairing` (
				`pre_id` ,
				`post_id` ,
				`pre_col` ,
				`post_col` 
				)
				VALUES (".$prepost['pre_id'].", ".$prepost['post_id'].", '".$prepost['pre_col']."', '".$prepost['post_col']."');";
		print $sql;
		$result = mysql_query($sql);
		if (!$result) {
			die(mysql_error());	
		}
	}
	
	
	function db_get_students_pre(){
		
		if(!$this->db) {
			db_connect();
		}
		
		$subjects = array();
		
		$sql = "select subject_id, forename, surname from pre_questions";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
		  $subjects[$row['subject_id']] = $row['forename'] . " " . $row['surname']; 
		} 
		return $subjects;
	}
	
	function db_get_students_post(){
		if(!$this->db) {
			db_connect();
		}
		
		$students = array();
		
		$sql = "select student_id, name from post_questions";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
		  $students[$row['student_id']] = $row['name']; 
		} 
		return $students;		
	}
	
	function map_students($prepost) {
		if(!$this->db) {
			db_connect();
		}
		$sql = "INSERT INTO  `student_pre_post_mapping` (
				`subject_id_pre` ,
				`student_id_post`
				)
				VALUES (".$prepost['subject_id'].", ".$prepost['student_id'].");";
		print $sql;
		$result = mysql_query($sql);
		if (!$result) {
			die(mysql_error());	
		}
	}
	
	function db_get_mapped_students() {
		if(!$this->db) {
			db_connect();
		}
		
		$students = array();
		
		$sql = "select forename, surname from pre_questions, student_pre_post_mapping where subject_id = subject_id_pre order by subject_id desc";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
		  $students[] = $row['forename'] . " " .$row['surname']; 
		} 
		
		return $students;		
	}
	
	// indexed by pre - post is the content
	function db_get_mapped_student_ids() {
		if(!$this->db) {
			db_connect();
		}
		
		$students = array();
		
		$sql = "select subject_id_pre, student_id_post from student_pre_post_mapping";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
		  $students[$row['subject_id_pre']] = $row['student_id_post']; 
		} 
		
		return $students;		
	}
	
	function db_escape($var) {
		$var = mysql_real_escape_string($var);
		return $var;	
	}
}
?>