<?php
require_once("DatabaseUtility.php");
$db = new DatabaseUtility();

$pre = $db->db_get_students_pre();
$post = $db->db_get_students_post();
$pairings = $db->db_get_mapped_students();
?>

<form action="map_students_pre_post_save.php" method="post">
<table>
	<Tr>
    	<td>Pre </td>
    </Tr>
    <tr>
    	
        <td >
        	<select name="subject_id" >
  
			<?php
                foreach ($pre as $pre_id=>$pre_text) {?>
					<option value="<?php print $pre_id;?>"><?php print $pre_text;?></option><?php
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
    	<td>
        	<select name="student_id">
  
			<?php
                foreach ($post as $post_id=>$post_text) {?>
					<option value="<?php print $post_id;?>"><?php print $post_text;?></option>	<?php
		                             
          		}
            ?>	</optgroup>
            </select>
        </td>  
    </tr>
    <tr>
    	<td><input type="submit" /></td>
    </tr>
</table>
</form>

<p>
Pre but no Post
<ul>
	<li>Joe Cox</li>
	<li>Laurie Green</li>
	<li>Darius Ilkhani</li>
	<li>Steven Warren</li>
	<li>Sean Watt</li>
</ul>
</p>
<p>
Post but no Pre
<ul>
	<li>Alistair Hay</li>
	<li>Stuart Gillies</li>
</ul>
</p>

<p>
<?php
		
foreach ($pairings as $key=>$var) {
	print $var . "<br>";
	
}
?>
</p>

