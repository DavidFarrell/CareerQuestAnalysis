<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();


if ( is_null($_GET['id']) ) {
	die("No id passed");	
}

$answers = $db->db_get_bev_question_for_grading($_GET['id']);

// indexed by pre - post is the content
$student_mapping = $db->db_get_mapped_student_ids();


$student = $_GET['student'];
if ( $student == null) {
	$student = 180;	
}


$bev_ratings = $db->db_get_bev_question_grading_submissions($_GET['id'], $student);


	/*
			[index by nothing]->array for the question
			ArrayForTheQuestion { [pre_id], [post_id], [pre_col], [post_col], [pre_text], [post_text], [pre_answers](array), [post_answers](array) }
			
				The [pre_answers] array is indexed by the student id in the pre_questions table
				The [post_answers] array is indexed by the student id in the post_questions table
		*/
		

?><head>

<style>
a {
	text-decoration:none;
	color:	#09F;
}

table
{
border-spacing:0;
font-family:Arial, Helvetica, sans-serif;
padding:0;
box-shadow:2px 2px 5px 1px #DBD9DB;
}

th
{
color:#404040;
background:#E6E6E6;
font-size:18px;
font-weight:100;
padding:0 10px;
text-align:left;
vertical-align:top;
}

tr
{
color:#787878;
font-size:16px;
font-weight:100;
}

td
{
padding:0 10px;
text-align:left;
vertical-align:top;
max-width:500px;
}

td.answer 
{
color:#565656;
padding-bottom:20px;
}


</style>

</head>


<h1>Question: <?php print $_GET['id'];?> - Student <?php print $student; ?>(<?php print $student_mapping[$student];?>)</h1>
<a href="bev_pre_post_question_list.php">&lt;Back to Question List&gt;</a>
<form action="bev_grade_pre_post_submit.php" method="post">
<input type="hidden" name="subject_id" value="<?php print $student; ?>"  />
<table>
	<tr>
    	<td width="110">
        	
        <?php 
			$previous_student = $student-1;
			while ($previous_student == 186 || $previous_student == 192 ||
  				   $previous_student == 193 || $previous_student == 209 ||$previous_student == 210 ) {
				
				$previous_student--;
			}
			if ($previous_student > 179) {?>
			 <a href="bev_grade_pre_post_questions.php?id=<?php print $_GET['id'];?>&student=<?php print $previous_student;?>" >&lt;Previous&gt;</a>
			
		<?php	
			}
		?></td>
    	<th width="300">Pre Question</th>
        <th width="100">Post Question</th>
        <th>Score</th>
       	<th>Confidence?</th>
       	<th>Comments</th>
        <td>
        <?php 
			$next_student = $student+1;
			while ($next_student == 186 || $next_student == 192 ||
  				   $next_student == 193 || $next_student == 209 ||$next_student == 210 ) {
				
				$next_student++;
			}
			if ($next_student < 209) {?>
             <a href="bev_grade_pre_post_questions.php?id=<?php print $_GET['id'];?>&student=<?php print $next_student;?>" >&lt;Next&gt;</a>
			
		<?php	
			}
		?>

             
        </td>
    </tr>
    
    <?php
	$current_row = 0;
	foreach ($answers as $index=>$answer) {
	?>
	<tr>
    	<td></td>
		<td><?php ($current_row == 0)?print $answer['pre_text']:"";?></th>
        <td><?php print $answer['post_text'];?></th>
    </tr>
    
    
    <?php
	
	// have we submitted ratings aready?
	if (sizeof($bev_ratings[$answer['post_col']]) > 0) {
	//	print "size of " . $answer['post_col'] . " is " . sizeof($bev_ratings[$answer['post_col']]);
		//print_r ($bev_ratings[$answer['post_col']]);
		
		
		// if so, we just show bev's submitted ratings
		?>
        <tr>
			<td><b>Answer</b></td>
			<td class="answer"><?php ($current_row == 0)?print $answer['pre_answers'][$student]:"";?></td>
			<td class="answer"><?php print $answer['post_answers'][ $student_mapping[$student]  ];?></td>
			<td>
            	<?php
					switch($bev_ratings[$answer['post_col']]['score'])    {
						case -2:
							echo "Much Worse";
							break;
						case -1:
							echo "Slightly Worse";
							break;
						case 0:
							echo "The Same";
							break;
						case 1:
							echo "Slightly Better";
							break;
						case 2:
							echo "Much Better";
							break;
						case 99:
							echo "Not Applicable";
							break;
					}
				?>
				
			</td>
			<td>
            <?php
					switch($bev_ratings[$answer['post_col']]['confidence'])    {
						
						case 0:
							echo "Not Confident";
							break;
						case 1:
							echo "Quite Confident";
							break;
						case 2:
							echo "Very Confident";
							break;
					}
				?>
				
			</td>
			<td>
				<?php
					print $bev_ratings[$answer['post_col']]['comments'];
				?>
			</td>
			<td><a href="bev_grade_pre_post_delete.php?pre_col=<?php print $_GET['id']?>&student=<?php print $student; ?>&post_col=<?php print $answer['post_col']; ?>"><font color="red">[x]</font></a></td>
		</tr>
        
        
        <?php		
	} else {
		
		// the row below is for when we DO show the rating system.  If ratings already submitted, we don't
		?>
		<tr>
			<td><b>Answer</b></td>
			<td class="answer"><?php ($current_row == 0)?print $answer['pre_answers'][$student]:"";?></td>
			<td class="answer"><?php print $answer['post_answers'][ $student_mapping[$student]  ];?></td>
			<td>
				<select name="score,<?php print $answer['pre_col']; ?>,<?php print $answer['post_col']; ?>">
					<option value="-2">Much Worse</option>
					<option value="-1">Slightly Worse</option>
					<option value="0">The Same</option>
					<option value="1">Slightly Better</option>
					<option value="2">Much Better</option>
					<option value="99">Not Applicable</option>
				</select>
			</td>
			<td>
				<select name="confidence,<?php print $answer['pre_col']; ?>,<?php print $answer['post_col']; ?>">
					<option value="0">Not Confident</option>
					<option value="1">Quite Confident</option>
					<option value="2">Very Confident</option>
				</select>
			</td>
			<td>
				<textarea name="comments,<?php print $answer['pre_col']; ?>,<?php print $answer['post_col']; ?>" rows="7" placeholder="Write here about special cases or questions, or whatever else we need to know."></textarea>
			</td>
			<td></td>
		</tr>
	<?php
	}
	?>
    
    <tr>
    	<td></td>
    	<td colspan="2"><hr /></td>

    </tr>
	<?php 
		$current_row++;
	}
	
	?>
    <tr>
    	<td></td>
    	<td colspan="5">
        	<input type="submit" value="Save" />
        </td>
    </tr>
</table>
</form> 

<?php //print_r ($answers) ; ?>