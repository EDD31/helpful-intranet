<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

$slug = pods_url_variable(0);
if ($slug=="homepage"){
	wp_redirect('/');
};
if ($slug=="control"){
	wp_redirect('/');
};

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<?php
	
	$chapter_header = false;
	$singletask = false;
	$pagetype = "";
	
	$taskpod = new Pod('task', $id);
	$current_task = $id;
	$parent_guide = $taskpod->get_field('parent_guide'); 
	$parent_guide_id = $parent_guide[0]['ID']; 
	if (!$parent_guide_id){
				$parent_guide_id = $post->ID;
	}
	$children_chapters = $taskpod->get_field('children_chapters'); 
	$current_attachments = $taskpod->get_field('document_attachments');
	
	if (!$parent_guide && !$children_chapters){
		$singletask=true;
		$pagetype = "task";
	}
	else {
		$pagetype = "guide";
	};

	if ($children_chapters && $parent_guide==''){
		$chapter_header=true;
	}

	if ($parent_guide){

	$parent_slug=$parent_guide[0]['post_name'];
	$parent_name=govintranetpress_custom_title($parent_guide[0]['post_title']); 
	$guidetitle =$parent_name;	
	}
	if (!$parent_guide){
	$guidetitle = govintranetpress_custom_title($taskpod->get_field("post_title"));
	}	
	

?>
	
	<div class="row">
		<div class="eightcol white last" id='content'>
						<div class="row">
							<div class='breadcrumbs'>
							<?php if(function_exists('bcn_display') && !is_front_page()) {
								bcn_display();
							}?>
							</div>
							
				</div>
			<div class="content-wrapper">
						

			<h1 class="taglisting <?php echo $pagetype; ?>"><?php echo $guidetitle; ?> <span class="title-cat"><?php echo ucwords($pagetype); ?></span></h1>

			</div>
<?php 
if ($pagetype=="guide"):

?>
		<div class="threecol">
			<div class="chapters">
    <nav role="navigation" class="page-navigation">
    <ol>
<?php
if ($chapter_header){
						echo "<li class='active'>";
						echo "<span class='part-label'>Part 1</span><br><span class='part-title'>".$guidetitle."</span>";
						}
		else {
				$podchap = new Pod('task', $parent_guide_id); 
				$children_chapters = $podchap->get_field('children_chapters'); 				
				$chapname = $parent_name;
				$chapslug = $parent_slug;
				echo "<li><a href='/task/{$chapslug}'><span class='part-label'>Part 1</span><br><span class='part-title'>{$chapname}</span></a>";
				}
				echo "</li>";

			$totalchapters = count($children_chapters);
			$carray = array();
			$k=1; 
			foreach ($children_chapters as $chapt)
			{
				if ($chapt['post_status']=='publish'){
				$k++;
				echo "<li ";
				if (pods_url_variable(1) == $chapt['post_name']){
					 echo "class='active'";
					 $current_chapter=$k;
				}
				echo ">";
				$chapname = govintranetpress_custom_title($chapt['post_title']);
				$chapslug = $chapt['post_name']; 
				$carray[$k]['chapter_number']=$k;
				$carray[$k]['slug']=$chapslug;
				$carray[$k]['name']=$chapname;
				if ($chapt['ID']==$current_task){
				echo "<span class='part-label'>Part {$k}</span><br><span class='part-title'>{$chapname}</span>";
				}
				else {
				echo "<a href='/task/{$chapslug}'><span class='part-label'>Part {$k}</span><br><span class='part-title'>{$chapname}</span></a>";
					
				}
				echo "</li>";
				}
				
			}
?>



	  </ol>
	  </nav>
</div>
		</div>
<?php
endif;
	if ($pagetype=="guide"){
		echo "<div class='ninecol last'>";
		echo "<div class='content-wrapper-notop'>";
						if ($current_chapter>1){
					echo "<h2>".get_the_title()."</h2>";
				}
				else {
					echo "<h2>Overview</h2>";
				}

			the_content(); 		

			if ($current_attachments){
				foreach ($current_attachments as $a){
				echo "<div class='downloadbox'>";
				echo "<p><a href='".$a['guid']."'>".$a['post_title']."</a></p>";
				echo "</div>";
				}
			}

			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
		echo "</div>";
		
        echo "<div class='content-wrapper-notop'><div class='guidepagination' role='navigation'>";
        echo "<ul class='group'>";
        if ($chapter_header){ // if on chapter 1
	        echo "<li class='previous'>";
	        echo "<span>You are at the beginning of this guide</span>";
	        echo "</li>";
	        echo "<li class='next'>";
	        echo "<span class='pagination-label'>Part 2</span><br><a href='/task/".$carray[2]['slug']."' title='Navigate to next part'>";
	        echo "<span class='pagination-part-title'>".$carray[2]['name']."</span>";
	        echo "</a>";
	        echo "</li>";        
        } 
        elseif ($current_chapter==2) { // if on chapter 2
        	echo "<li class='previous'>";
            echo "<span class='pagination-label'>Part 1</span><br><a href='/task/".$parent_slug."' title='Navigate to previous part'>";
            echo "<span class='pagination-part-title'>".govintranetpress_custom_title($parent_name)."</span>";
            echo "</a>";
            echo "</li>";
            if ($carray[3]['slug']){
	        	echo "<li class='next'>";
	            echo "<span class='pagination-label'>Part 3</span><br><a href='/task/".$carray[3]['slug']."' title='Navigate to next part'>";
	            echo "<span class='pagination-part-title'>".$carray[3]['name']."</span>";
	            echo "</a>";
	            echo "</li>"; 
	        }
	        else
	        {    
	        echo "<li class='next'>";
	        echo "<span>You are at the end of this guide</span>";
	        echo "</li>";
	            }       
        }
        else {
        	$previous_chapter = $current_chapter-1; 
        	$next_chapter = $current_chapter+1;
        	echo "<li class='previous'>";
            echo "<span class='pagination-label'>Part {$previous_chapter}</span><br><a href='/task/".$carray[$previous_chapter]['slug']."' title='Navigate to previous part'>";
            echo "<span class='pagination-part-title'>".govintranetpress_custom_title($carray[$previous_chapter]['name'])."</span>";
            echo "</a>";
            echo "</li>";
            if ($carray[$next_chapter]['slug']){
	        	echo "<li class='next'>";
	        	echo "<span class='pagination-label'>Part {$next_chapter}</span><br><a href='/task/".$carray[$next_chapter]['slug']."' title='Navigate to next part'>";
	        	echo "<span class='pagination-part-title'>".govintranetpress_custom_title($carray[$next_chapter]['name'])."</span>";
	            echo "</a>";
	            echo "</li>"; 
	            }    
	        else {
		    echo "<li class='next'>";
	        echo "<span>You are at the end of this guide</span>";
	        echo "</li>";

	        }   
        }
        
        
        echo "</ul>";
        echo "</div></div><div class='clearfix'></div>";


	} else {
		echo "<div class='twelvecol last'>";
		echo "<div class='content-wrapper'>";

		the_content(); 

			if ($current_attachments){
			echo "<h3>Downloads</h3>";
				foreach ($current_attachments as $a){
				echo "<div class='downloadbox'><div class='downloadicon'>";
				echo "<p><a href='".$a['guid']."'>".$a['post_title']."</a></p>";
				echo "</div></div>";
				}
			}

			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
		echo "</div>";
	}


			
			 ?>

			</div>
			
		</div> <!--end of first column-->
		
		<div class="fourcol last" >	


				<?php 
				$podtask = new Pod('task', $id);
				$related_links = $podtask->get_field('related_tasks');
				if ($related_links){
				echo "<div class='widget-box list'>";
				echo "<h3 class='widget-title'>Related tasks and guides</h3>";
				echo "<ul>";
				foreach ($related_links as $rlink){
					if ($rlink['post_status'] == 'publish') {
							$taskpod = new Pod ('task' , $rlink['ID']);
							$taskparent=$taskpod->get_field('parent_guide');
							$title_context="";
							if ($taskparent){
								$parent_guide_id = $taskparent[0]['ID']; 		
								$taskparent = get_post($parent_guide_id);
								$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
							}		
					echo "<li><a href='/task/".$rlink['post_name']."'>".govintranetpress_custom_title($rlink['post_title']).$title_context."</a></li>";
					}
				}
				echo "</ul></div>";
				}

 ?>
			
			
			<?php
$post_categories = wp_get_post_categories( $parent_guide_id );
$cats = array();
$catsfound = false;	
	$catshtml='';
	foreach($post_categories as $c){
	$cat = get_category( $c );
	$catsfound = true;
	$catshtml.= "<span class='wptag t".$cat->term_id."'><a href='/task-by-category/?cat=".$cat->slug."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
	}
	
if ($catsfound){
	echo "<div class='widget-box'><h3>Categories</h3><p class='taglisting page'>".$catshtml."</p></div>";
}

				$posttags = get_the_tags($parent_guide_id);
				if ($posttags) {
					$foundtags=false;	
					$tagstr="";
				  	foreach($posttags as $tag) {
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = $tag->slug;
					    	$tagstr=$tagstr."<span class='wptag'><a href='/tagged/?tag={$tagurl}&amp;posttype=task'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
				    	}
				  	}
				  	if ($foundtags){
					  	echo "<div class='widget-box'><h3>Tags</h3><p> "; 
					  	echo $tagstr;
					  	echo "</p></div>";
				  	}
				}

		 	dynamic_sidebar('task-widget-area'); 

?>			
				
				
	
			</div> 

				</div>
		</div>				
			</div> <!--end of second column-->


				
	</div> 

			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>