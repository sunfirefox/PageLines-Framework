<?php


class PageLinesDashboard {
	
	
	function __contruct(){
		
		
		
	}
	
	function draw(){
		
		// Updates Dashboard
		
		$dashboards = '';
		
		$updates = $this->get_updates(); 
		
		$args = array(
			'title'	=> 'Your Available Updates', 
			'data'	=> $updates, 
			'icon'	=> PL_ADMIN_ICONS . '/download.png',
			'excerpt-trim'	=> 0
		); 

		$dashboards = $this->dashboard_pane('updates', $args); 
		
		// PageLines Blog Dashboard
		
		$args = array(
			'title'	=> 'News from the PageLines Blog', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half pl-dash-space', 
			'icon'	=> PL_ADMIN_ICONS . '/welcome.png'
		); 
		
		$dashboards .= $this->dashboard_pane('news', $args);
		
		// PageLines Store Latest Dash
		
		$args = array(
			'title'	=> 'Latest on PageLines Store', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half', 
			'icon'	=> PL_ADMIN_ICONS . '/store.png'
		); 
		
		$dashboards .= $this->dashboard_pane('store', $args);
		
		// Latest from the Community
		$args = array(
			'title'	=> 'From the Community', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half pl-dash-space', 
			'icon'	=> PL_ADMIN_ICONS . '/users.png'
		); 
		
		$dashboards .= $this->dashboard_pane('community', $args);
		
		// PageLines Plus
		$args = array(
			'title'	=> 'PageLines Extensions', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half', 
			'icon'	=> PL_ADMIN_ICONS . '/plusbtn.png'
		); 
		
		$dashboards .= $this->dashboard_pane('extensions', $args);
		
		
		return $this->dashboard_wrap($dashboards); 
		
	}
	
	function dashboard_wrap( $dashboards ){
		
		return sprintf('<div class="pl-dashboards fix">%s</div>', $dashboards);
		
	}
	
	function wrap_dashboard_pane($id, $args = array()){
		return sprintf('<div class="pl-dashboards fix">%s</div>', $this->dashboard_pane( $id, $args ));
	}
	
	
	function dashboard_pane( $id, $args = array() ){
		
		$defaults = array(
			'title' 		=> 'Dashboard',
			'icon'			=> PL_ADMIN_ICONS.'/pin.png',  
			'classes'		=> '', 
			'data'			=> array(), 
			'data-format'	=> 'array', 
			'excerpt-trim'	=> 18
		);
		
		$a = wp_parse_args($args, $defaults); 
		
		ob_start()
		?>
		<div id="<?php echo 'pl-dash-'.$id;?>" class="pl-dash <?php echo $a['classes'];?>">
			<div class="pl-dash-pad">
				<div class="pl-vignette">
					<h2 class="dash-title"><?php printf('<img src="%s"/> %s', $a['icon'], $a['title']); ?></h2>
					<?php echo $this->dashboard_stories( $a ); ?>
				</div>
			</div>
		</div>
		<?php 
		
		return ob_get_clean();
		
	}
	
	function dashboard_stories( $args = array() ){
		
		if($args['data-format'] == 'array')
			return $this->stories_array_format($args); 
		
		
	}
	
	function stories_rss_format(){
		
	}
	
	function stories_array_format($args){
		
		ob_start();
		
		$count = 1;
		foreach($args['data'] as $id => $story){
			
			$image = (isset($story['img'])) ? $story['img'] : false; 
			$tag = (isset($story['tag'])) ? $story['tag'] : false; 
			$tag_class = (isset($story['tag-class'])) ? $story['tag-class'] : ''; 

			$alt = ($count % 2 == 0) ? 'alt-story' : '';
			
			$excerpt = ( $story['text'] ) ? $story['text'] : '';
			if ( $excerpt )
				$excerpt = (!$args['excerpt-trim']) ? $story['text'] : custom_trim_excerpt($story['text'], $args['excerpt-trim']);
		?>
		<div class="pl-dashboard-story media <?php echo $alt;?> dashpane">
			<div class="dashpane-pad fix">
				<?php
					if($tag) {
						
						$button = $this->get_upgrade_button( $story['data'] );

						printf('<div class="img">%s</div>', $button );
						
						
						
					} elseif($image)
						printf('<div class="img img-frame"><img src="%s" /></div>', $image);
				
				?>
				<div class="bd">
					<h3><?php echo $story['title'];?></h3>
					<p><?php echo $excerpt; ?></p>
				</div>
			</div>
		</div>
		
		<?php
		$count++;
		}
		
		return ob_get_clean();
	}
	
	
	function get_upgrade_button( $data ) {
		
		global $extension_control;
						
		$type = rtrim( $data->type, 's' );

						
		$file = ( 'section' === $type ) ? $data->class : $data->slug;
							
			$o = array(
				'mode'	=> 'upgrade',
				'case'	=> sprintf( '%s_upgrade', $type ),
				'text'	=> 'Upgrade Now',
				'type'	=> $type,
				'file'	=> $data->slug,
				'path'	=> $file,
				'dtext'	=> sprintf( 'Upgrading to version %s', $data->version ),
				'condition'	=> 1,
				'dashboard'	=> true
			);

		$button = $extension_control->ui->extend_button( $data->slug, $o);									
		return $button;
	}
	
	function stories_remote_url_format(){
		
	}
	
	function test_array(){
		
		$data = array(
			'story1'	=> array(
				'title'	=> 'Test Story 1', 
				'text'	=> 'Here is a bunch of text for the first text story that will go in the admin. Simon is gonna have to work with this to get the rest figured out. That is all i have to say.', 
				'link'	=> 'http://www.pagelines.com/about'
			), 
			'story2'	=> array(
				'title'	=> 'Test Story 3', 
				'text'	=> 'Here is a bunch of text for the first text story that will go in the admin. Simon is gonna have to work with this to get the rest figured out. That is all i have to say.', 
				'link'	=> 'http://www.pagelines.com/about', 
				'img'	=> PL_ADMIN_IMAGES . '/pagelines-icon.jpg'
			),
			'story3'	=> array(
				'title'	=> 'Test Story 3', 
				'text'	=> 'Here is a bunch of text for the first text story that will go in the admin. Simon is gonna have to work with this to get the rest figured out. That is all i have to say.', 
				'link'	=> 'http://www.pagelines.com/about'
			)
		);
		
		return $data;	
	}
	
	function get_updates() {
		
		$default['story0'] = array(
			'title'	=>	__( 'No updates at this time, sorry i wish we could do more.', 'pagelines' ),
			'text'	=>	false
		);
		
		$updates = json_decode( get_theme_mod( 'pending_updates' ) );
		
		if( !is_object( $updates ) )
			return $default;
		
		$data = array();
		$a = 0;
		foreach( $updates as $key => $update ) {
			
			$data["story$a"] = array(
				
				'title'		=>	$update->name,
				'text'		=>	$update->changelog,
				'tag'		=>	$update->type,
				'data'		=>	$update
				
			);		
			$a++;	
		}
	if( empty( $data ) )
		return $default;
	return $data;
	}	
}