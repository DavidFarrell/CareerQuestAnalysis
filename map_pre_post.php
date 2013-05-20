

<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();

$fulltext = $db->db_get_full_text();
$pre_questions = $db->db_get_pre_questions();
$post_questions = $db->db_get_post_questions();
$pairings = $db->db_get_pre_post_pairing()
?>

<form action="map_pre_post_save.php" method="post">
<table>
	<Tr>
    	<td>Pre Question</td>
    </Tr>
    <tr>
    	
        <td colspan="2">
        	<select name="pre_id" id="text1">
            	<optgroup label="First Set">
  
			<?php
                foreach ($fulltext as $pre_id=>$pre_text) {?>
					<option value="<?php print $pre_id;?>"><?php print $pre_text;?></option><?php
					if ($pre_id == 34) {
						print "</optgroup> <optgroup label='Second Set'>";	
					}
					?>	                
            <?	}
				
            ?>	
            	</optgroup>
            </select>			
        </td>
	</tr>
    <tr>
    	<td>Post Question</td>
    </tr>
    <tr>
    	<td colspan="2">
        	<select name="post_id" id="text2">
            	<optgroup label="First Set">
  
			<?php
                foreach ($fulltext as $post_id=>$post_text) {?>
					<option value="<?php print $post_id;?>"><?php print $post_text;?></option>	<?php
					if ($post_id == 34) {
						print "</optgroup> <optgroup label='Second Set'>";	
					}
					?>	                                
            <?	}
            ?>	</optgroup>
            </select>
        </td>  
    </tr>
    <tr>
    	<td>Pre Column</td><td>Post Column</td>
    </tr>
    <tr>
        
        <td>
			<select name="pre_col" onChange="matchPulldown();" id="box1">
			<?php
                foreach ($pre_questions as $pre_id=>$pre_text) {?>
				<option value="<?php print $pre_text;?>"><?php print $pre_text;?></option>	                
            <?	}
            ?>	
            </select>
        </td>
        <td>
			<select name="post_col" id="box2">
			<?php
                foreach ($post_questions as $post_id=>$post_text) {?>
				<option value="<?php print $post_text;?>"><?php print $post_text;?></option>	                
            <?	}
            ?>	
            </select>
        </td>      
    </tr>
    <tr>
    	<td><input type="submit" /></td>
    </tr>
</table>
</form>

<p>
<?php
/*
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
	
	?*/

// [preid][precol]
// [preid][postcol]
		
foreach ($pairings as $key=>$var) {
	print $var['pre_col'] . " = " . $var['post_col'] . "<br>";
	
}
?>
</p>


<script>



function sync(el1, el2) {
    if (!el1) {
        return false;
    }
    else {
        var val = el1.value;
        var syncWith = document.getElementById(el2);
        var options = syncWith.getElementsByTagName('option');
        for (var i = 0, len = options.length; i < len; i++) {
            if (options[i].value == val) {
                options[i].selected = true;
            }
        }
    }
}

var selectToSync = document.getElementById('box1');
selectToSync.onchange = function(){
    sync(this,'box2');
};


var selectToSync = document.getElementById('text1');
selectToSync.onchange = function(){
    sync(this,'text2');
};
</script>