<?php

/*
Plugin Name: Begen Gec WP Plugin
Plugin URI: http://www.hamdicatal.com/
Description: Wordpress tabanlı beğenme sistemidir
Version: 1.0
Author: Hamdi ÇATAL
Author URI: http://www.hamdicatal.com/
License: GPL3
*/

//İçerik altına beğen butonu koyar
function BegenButonu($icerik)
{
	$icerik .= "<div id='Begenbutonu' class='begen_butonu' yaziid='".get_the_ID()."'>Beğen (".get_post_meta(get_the_ID(),"begenbutonu")[0].")</div>	";
	return $icerik;
}

add_filter('the_content','BegenButonu'); // content filtrele

// Stil ve JS dosyalarını header ve footer'a include eder
function Buton_Stil()
{
	wp_enqueue_style('likestyle', plugin_dir_url(__FILE__).'style.css', false);
}

function Buton_JS()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('likejs',plugin_dir_url(__FILE__).'likebuton.js', false);
}

add_action('wp_enqueue_scripts', 'Buton_Stil');
add_action('wp_footer', 'Buton_JS');

//Etiketlerdeki yazı sayıları, etiketin kullanım sayısı

function get_posts_count_by_tag($tag_name)
{
    $tags = get_tags(array ('search' => $tag_name) );
    foreach ($tags as $tag) {
      if ($tag->name == $tag_name) {
         return $tag->count;
      }
    }
    return 0;
}

//Etiket ve beğenilerin bulunduğu sayfayı oluşturur
function LikeTagListe($content){
	global $wpdb;
	if(strpos($content,"[liketagliste]")){ // listeleme yapmak için content içine eklenen BB code
		$content = "";
		$yazilar = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE `meta_key` LIKE 'begenbutonu' " ); //sql de arar, begen butonlarının id si ve begeni sayısını verir
		$taglar = array();
		foreach($yazilar as $yazi){
				$posttags = get_the_tags($yazi->post_id); // post un etiketlerini al
				if ($posttags) {
				  foreach($posttags as $tag) { // her bir etiketi ve beğeni sayısını array e aktarır
						if(!isset($taglar[$tag->name])){
						$taglar[$tag->name] = 0;
						}
						$taglar[$tag->name] = $taglar[$tag->name]+$yazi->meta_value;
				  }
				}
		}
		$yenitaglar = array();
		foreach ($taglar as $key => $row){
				$yenitaglar[$key] = $row;
			}
		array_multisort($yenitaglar, SORT_DESC, $taglar); // etiketler sıralanır
		
		$content .= "<table border='1'>
						<tr>
							<th>Anahtar Kelime</th>
							<!-- <td>Toplam</td> -->
							<th>Toplam Beğeni</th>
						</tr>
					";
				
		if($_GET["sayfa"] == "" || $_GET["sayfa"] == 1)	// linkten id sini alır??
		{ 
			$counter = 0;	
			$c = $counter;	
		}
		
		else
		{
			$counter = $_GET["sayfa"];
			$c = ($counter-1)*10;
		}
		
		$sayac = 0;
		
		foreach($yenitaglar as $key => $lyazi)
		{
			$counter++;
			
			if ($counter <= $c+1 || $counter-1 >  $c+10 )
				continue;
			
			$content .= '
				<tr>
					<td>'.$key.'</td>
					<!-- <td>'.get_posts_count_by_tag($key).'</td> -->
					<td>'.$lyazi.'</td>
				</tr>
			';
			
			$sayac++;
			
			if($sayac == 10)
			{
				$counter++;
				$sayac = 0;
			}
		}
		
		$content .= "</table>";
		$content .= "<table border='0'><tr><td>Sayfa: </td><td><ul class='sayfala'>";
		
		for( $i = 1 ; $i <= count ( $yenitaglar ) / 10 ; $i++ )
		{
			$content .= "<li><a href='".get_the_permalink()."?sayfa=".$i."'>".$i."</a></li>";
		}
		
		if(count ( $yenitaglar ) % 10 > 0)
			$content .= "<li><a href='".get_the_permalink()."?sayfa=".$i."'>".$i."</a></li>";
		$content .= "</ul></td></tr></table>";
	}
	
	return $content;
	
}

add_filter('the_content','LikeTagListe');


add_action( 'widgets_init', 'wp_begeni_widgets' );
 
function wp_begeni_widgets() {
 register_widget( 'wp_begeni_widget' );
}
 
class wp_begeni_widget extends WP_Widget {
 
function wp_begeni_widget() {
 
 /* Widget settings */
 $widget_ops = array( 'classname' => 'widget_sosyal', 'description' => __('Bu bileşenin herhangi bir ayarı bulunmamaktadır.', 'bi3') );
 
/* Create the widget */
 $this->WP_Widget( 'wp_begeni_widget', __('Popüler Yazılar (Like)', 'bi3'), $widget_ops );
 }
 
function widget( $args, $instance ) {
 
 ?>
    <div class="widget">
	<h2>Popüler Yazılar</h2>
<ul>

<?php 

global $wpdb;

$result = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE `meta_key` LIKE 'begenbutonu' ORDER BY meta_value DESC"); 

$c = 0;

foreach ($result as $post)
{ 

	if($c == 10 )
		break;
	
	$postid = $post->post_id; 
	$title = get_the_title($postid); 
	
	?> 

	<li><a href="<?php echo get_permalink($postid); ?>" title="<?php echo $title ?>">

	<?php echo $title ?></a>
	
		<?php echo " (".$post->meta_value.")" ?>
		
			</li>
		
	<?php 
	
	$c++;
	
	} 
	
	 ?>
	
	</ul></div>
	
 <?php
 echo $after_widget;
 }
 
function update( $new_instance, $old_instance ) {}
 
function form( $instance ) {
 
 $instance = wp_parse_args( (array) $instance, $defaults ); ?>
 
 <p>
 Bu bileşenin herhangi bir ayarı bulunmamaktadır.
 </p>
 
 <?php
 }
}
 
?>