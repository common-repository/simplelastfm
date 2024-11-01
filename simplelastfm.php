<?php
/*
Plugin Name: simplelastfm
Description: Displaying few recent tracks and most played artists.
Using simplexml to load data from last.fm.
Version: 1.0
Author: Adam Huchla
License: GPL2 or later
*/
function topartists ($j, $user) {
    $lasty = simplexml_load_file('http://ws.audioscrobbler.com/2.0/user/'.$user.'/topartists.xml')
    or die('error while connecting with last.fm');
    echo '<h4>Top'.$j.' artists:</h4><ul>';
    $bachor = $lasty->children();
    for($i = 0; $i<$j ; $i++){
	     $bg = ($i%2==0) ? '#888' : '#ddd';
	     echo '<li style="background-color:'.$bg.'"><strong>'.$bachor[$i]->name.'</strong><br/>played: '.$bachor[$i]->playcount.' times.</li>';
	     };
    echo "</ul>";
	  };
function lastplayed ($j, $user) {
    $lasty = simplexml_load_file('http://ws.audioscrobbler.com/1.0/user/'.$user.'/recenttracks.rss')
    or die('error while connecting with last.fm');
    echo '<h4>Last played:</h4><ul>';
    $bachor = $lasty->children()->children();
    for($i = 0; $i<$j ; $i++){
        $bg = ($i%2==0) ? '#888' : '#ddd';
	      echo '<li style="background-color:'.$bg.'"><strong>'.$bachor[$i+8]->children().'</strong></li>';
        };
    echo '</ul>';    
  };
class wp_simplelastfm extends WP_Widget {
function wp_simplelastfm() {
		$widget_ops = array('classname' => 'wp_simplelastfm', 'description' => 'Displaying few recent tracks and most played artists.' );
		$this->WP_Widget('wp_simplelastfm', 'Simplelastfm widget', $widget_ops);
	}
function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['user_id'] = strip_tags($new_instance['user_id']);
		$instance['artists']  = strip_tags($new_instance['artists']);
		$instance['songs']  = strip_tags($new_instance['songs']);
		return $instance;
	}
function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'user_id' => '', 'artists' => ' ', 'songs' => ' ' ) );
		$title = strip_tags($instance['title']);
		$user_id = strip_tags($instance['user_id']);
		$artists = strip_tags($instance['artists']);
		$songs = strip_tags($instance['songs']);
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('user_id'); ?>">Username: <input class="widefat" id="<?php echo $this->get_field_id('user_id'); ?>" name="<?php echo $this->get_field_name('user_id'); ?>" type="text" value="<?php echo attribute_escape($user_id); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('artists'); ?>">Number of artists: 
      <select id="<?php echo $this->get_field_id('artists'); ?>" name="<?php echo $this->get_field_name('artists'); ?>" type="text" value="<?php echo attribute_escape($artists); ?>">
       <?php 
          $selected = attribute_escape($artists);
          for($i=0;$i<11;$i++){
          echo '<option value="'.$i.'"'.($i==$selected ? 'selected="selected"' : '').'>'.$i.'</option>';
          }
        ?>
      </select>
  </label></p>
  <p><label for="<?php echo $this->get_field_id('songs'); ?>">Number of songs: 
      <select id="<?php echo $this->get_field_id('songs'); ?>" name="<?php echo $this->get_field_name('songs'); ?>" type="text" value="<?php echo attribute_escape($songs); ?>" >
        <?php 
          $selected = attribute_escape($songs);
          for($i=0;$i<11;$i++){
          echo '<option value="'.$i.'"'.($i==$selected ? 'selected="selected"' : '').'>'.$i.'</option>';
          }
        ?>
      </select>
  </label></p>
<?php
	}
function widget ($args, $instance) {
extract($args, EXTR_SKIP);
echo $before_widget;
		$widget_title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
		$user_id = empty($instance['user_id']) ? '&nbsp;' : apply_filters('widget_users_id', $instance['user_id']);
		$artists = empty($instance['artists']) ? '$nbsp;' : apply_filters('widget_artists', $instance['artists']);
		$songs = empty($instance['songs']) ? '$nbsp;' : apply_filters('widget_songs', $instance['songs']);
    echo '<div class="widget">';
    if ( $widget_title != '' && $widget_title != '&nbsp;' ) { echo $before_title . $widget_title . $after_title; };		
    if ($artists!=0) topartists($artists,$user_id);
    if ($songs!=0)   lastplayed($songs,$user_id);
    echo '</div>';
echo $after_widget;
}
} 
add_action( 'widgets_init', create_function('', 'return register_widget("wp_simplelastfm");') );
?>