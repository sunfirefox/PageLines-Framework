<?php 
/**
 * 
 *
 *  Options Layout Class
 *
 *
 *  @package PageLines Core
 *  @subpackage Options
 *  @since 4.0
 *
 */

class PageLinesOptionsUI {

/*
	Build The Layout
*/
	function __construct( $args = array() ) {
		
		$defaults = array(
				'title'			=> ( STYLESHEETPATH == TEMPLATEPATH ) ? 'Settings' : ucfirst( CHILDTHEMENAME ) . ' - Settings',
				'callback'		=> null,
				'settings'		=> PAGELINES_SETTINGS, 
				'show_save'		=> true,
				'show_reset'	=> true, 
				'title_size'	=> 'normal'
			);
		
		$this->set = wp_parse_args( $args, $defaults );

		// Set option array callbacks
		$this->option_array = (isset($this->set['callback'])) ? call_user_func( $this->set['callback'] ) : get_option_array();
		
		$this->primary_settings = ($this->set['settings'] == PAGELINES_SETTINGS) ? true : false;
		
		$this->tab_cookie = 'PLTab_'.$this->set["settings"];
		
		// Draw the thing
		$this->build_header();	
		$this->build_body();
		$this->build_footer();	
		
	}
		
		/**
		 * Option Interface Header
		 *
		 */
		function build_header(){?>
			<div class='wrap'>
				<table id="optionstable"><tbody><tr><td valign="top" width="100%">
			
				  <form id="pagelines-settings-form" method="post" action="options.php" class="main_settings_form">
		
							<?php 
								wp_nonce_field('update-options'); // security for option saving
								settings_fields($this->set['settings']); // namespace for options important!  
					 	
								echo OptEngine::input_hidden('input-full-submit', 'input-full-submit', 0); // submit the form fully, page refresh needed
					 	
								$this->get_tab_setup();
				
								$this->_get_confirmations_and_system_checking(); 
							?>
					
						<div class="clear"></div>
						<div id="optionsheader" class="fix">
							<div class="ohead" class="fix">
								<div class="ohead-pad fix">
									<div class="sl-black superlink-wrap">
										<a class="superlink" href="<?php echo home_url(); ?>/" target="_blank" title="View Site &rarr;">
											<span class="superlink-pagelines">&nbsp;</span>
										</a>
									</div>
									<div class="ohead-title">
										<?php echo apply_filters( 'pagelines_settings_main_title', $this->set['title'] ); ?> 
									</div>
									<div class="ohead-title-right">
										<?php if($this->set['show_save']):?>
										<div class="superlink-wrap osave-wrap">
											<input class="superlink osave" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" />
										</div>
										<?php endif;?>
									</div>
								</div>
						
					
							</div>
						</div>
				
		<?php }
		
		function _get_confirmations_and_system_checking(){
			
				// Load Ajax confirmation
				printf('<div class="ajax-saved" style=""><div class="ajax-saved-pad"><div class="ajax-saved-icon"></div></div></div>');
			
				// get confirmations
				pagelines_draw_confirms();
				
				// Get server error messages
				pagelines_error_messages();

		}
		
		/**
		 * Option Interface Footer
		 *
		 */
		function build_footer(){?>
				<div id="optionsfooter" class="fix">
					<div class="ohead fix">
						<div class="ohead-pad fix">
							<?php if($this->set['show_save']):?>
							<div class="superlink-wrap osave-wrap">
								<input class="superlink osave" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" />
							</div>
							<?php else:?>
								<div class="superlink-wrap">
									<a class="superlink" href="http://www.pagelines.com/"><span class="superlink-pad">Visit PageLines Site &rarr;</span></a>
								</div>
							<?php endif;?>
						</div>
					</div>
				</div>

				<?php if($this->set['show_reset']):?>
				<div class="optionrestore">
						<h4><?php _e('Restore Settings', 'pagelines'); ?></h4>
						<p>
							<div class="context">
								<?php echo OptEngine::superlink('Restore To Default', 'grey', 'reset-options', 'submit', 'onClick="return ConfirmRestore();"', get_pagelines_option_name('reset'));?>
								Use this button to restore these settings to default. (Note: Restore template and layout information in their individual tabs.)</div>
							<?php pl_action_confirm('ConfirmRestore', 'Are you sure? This will restore your settings information to default.');?>
						</p>

				</div>
				<?php endif;?>

				 
			  	</form><!-- close entire form -->

				<?php  if($this->primary_settings) $this->get_import_export(); ?>
				
			</td></tr></tbody></table>

			<div class="clear"></div>
			<script type="text/javascript">/*<![CDATA[*/
			jQuery(document).ready(function(){ jQuery('.framework_loading').hide(); });
			/*]]>*/</script>
			</div>
		<?php }
		
		/**
		 * Option Interface Body, including vertical tabbed nav
		 *
		 */
		function build_body(){
			$option_engine = new OptEngine( $this->set['settings'] );
			global $pl_section_factory; 
?>
			<div id="tabs">	
				<ul id="tabsnav">
					<li><span class="graphic top">&nbsp;</span></li>
					<?php 
					
					
					foreach($this->option_array as $menu => $oids){
						
						$bg = (isset($oids['icon'])) ? sprintf('style="background: transparent url(%s) no-repeat 0 0;"', $oids['icon']) : '';
						
						printf('<li><a class="%1$s tabnav-element" href="#%1$s"><span %3$s >%2$s</span></a></li>', $menu, ucwords( str_replace('_',' ',$menu)), $bg);
					}
					?>
					<li><span class="graphic bottom">&nbsp;</span></li>
					
					<div class="framework_loading"> 
						<a href="http://www.pagelines.com/forum/topic.php?id=6489#post-34852" target="_blank" title="Javascript Issue Detector">
							<span class="framework_loading_gif" >&nbsp;</span>
						</a>
					</div>
				</ul>
				
				<div id="thetabs" class="fix">
<?php 				if(!VPRO) $this->get_pro_call();
					 
					foreach($this->option_array as $menu => $oids){
						$bg = (isset($oids['icon'])) ? sprintf('style="background: transparent url(%s) no-repeat 10px 16px;"', $oids['icon']) : '';
						
						$is_htabs = ( isset($oids['htabs']) ) ? true : false;
						
						// The tab container start....
						printf('<div id="%s" class="tabinfo %s">', $menu, ($is_htabs) ? 'htabs-interface' : '');
					
							// Draw Menu Title w/ Icon
							if( stripos($menu, '_') !== 0 )
								printf('<div class="tabtitle" %s><div class="tabtitle-pad">%s</div></div>', $bg, ucwords(str_replace('_',' ',$menu)));
							
							
							// Render Options
							if( isset($oids['htabs']))
								OptEngine::get_horizontal_nav( $menu, $oids );
								
							elseif( isset($oids['metapanel']))
								echo $oids['metapanel'];
								
							else
								foreach( $oids as $oid => $o )
									if( $oid != 'icon' )
										$option_engine->option_engine($oid, $o);
								
								
						echo '<div class="clear"></div></div>';
					}
					?>	
				</div>
			</div>
<?php 	}
		
	function get_import_export(){ ?>
		
		<div class="optionrestore restore_column_holder fix">
			<div class="restore_column_split">
				<h4><?php _e('Export Settings', 'pagelines'); ?></h4>
				<p class="fix">
					<a class="button-secondary download-button" href="<?php echo admin_url('admin.php?page=pagelines&amp;download=settings'); ?>">Download Theme Settings</a>
				</p>
			</div>

			<div class="restore_column_split">
				<h4><?php _e('Import Settings', 'pagelines'); ?></h4>
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="settings_upload" value="settings" />
					<p class="form_input">
						<input type="file" class="text_input" name="file" id="settings-file" />
						<input class="button-secondary" type="submit" value="Upload New Settings" onClick="return ConfirmImportSettings();" />
					</p>
				</form>

				<?php pl_action_confirm('ConfirmImportSettings', 'Are you sure? This will overwrite your current settings and configurations with the information in this file!');?>
			</div>
		</div>
		
		
	<?php }	
	
	/**
	 *  Tab Stuff
	 */
	function get_tab_setup(){
		
		echo OptEngine::input_hidden('selectedtab', $this->set['settings'], load_pagelines_option('selectedtab', 0)); // tracks last tab active 
	
		if(isset($_GET['selectedtab']))
			$selected_tab = $_GET['selectedtab'];
		elseif(isset($_COOKIE[$this->tab_cookie]))
			$selected_tab = (int) $_COOKIE[$this->tab_cookie];
		elseif(pagelines_option('selectedtab'))
			$selected_tab = pagelines_option('selectedtab');
		else
			$selected_tab = 0;

		$this->get_tab_setup_script( $selected_tab );
	}
	
	function get_tab_setup_script( $selected_tab ){ ?>
		<script type="text/javascript">
				jQuery(document).ready(function() {						
					var myTabs = jQuery("#tabs").tabs({ fx: { opacity: "toggle", duration: "fast" }, selected: <?php echo $selected_tab; ?>});
					
					jQuery('#tabs').bind('tabsshow', function(event, ui) {
						var selectedTab = jQuery('#tabs').tabs('option', 'selected');
						jQuery("#selectedtab").val(selectedTab);
						jQuery.cookie('<?php echo $this->tab_cookie;?>', selectedTab);
						
					});
				});
		</script>
	<?php }
	
	function get_pro_call(){
		global $pl_section_factory; 
		
		$usections = $pl_section_factory->unavailable_sections;
		
		?>
	
		<div id="vpro_billboard" class="">
			<div class="vpro_billboard_height">
				<a class="vpro_thumb" href="<?php echo PROVERSIONOVERVIEW;?>"><img src="<?php echo PL_IMAGES;?>/pro-thumb-125x50.png" alt="<?php echo PROVERSION;?>" /></a>
				<div class="vpro_desc">
					<strong style="font-size: 1.2em">Get the Pro Version </strong><br/>
					<?php echo THEMENAME;?> is the free version of <?php echo PROVERSION;?>, a premium product by <a href="http://www.pagelines.com" target="_blank">PageLines</a>.<br/> 
					Buy <?php echo PROVERSION;?> for tons more options, sections and templates.<br/> 	
				
					<a class="vpro_link" href="#" onClick="jQuery(this).parent().parent().parent().find('.whatsmissing').slideToggle();">Pro Features &darr;</a>
					<a class="vpro_link" href="<?php echo PROVERSIONOVERVIEW;?>">Why Pro?</a>
					<a class="vpro_link"  href="<?php echo PROVERSIONDEMO;?>"><?php echo PROVERSION;?> Demo</a>
					<?php if(defined('PROBUY')):?><a class="vpro_link vpro_call"  href="<?php echo PROBUY;?>"><strong>Buy Now &rarr;</strong></a><?php endif;?>
				
				</div>
			
			</div>
			<div class="whatsmissing">
				 <h3>Pro Only Features</h3>
				<?php if(isset($usections) && is_array($usections)):?>
					<p class="mod"><strong>Pro Sections</strong> (drag &amp; drop)<br/>
					<?php 
						foreach( $usections as $unavailable_section )
							echo $unavailable_section->name;if($unavailable_section !== end($usections)) echo ' &middot; ';?>
					</p>
				<?php endif;?>
				
				<?php 
				$unavailable_section_areas = get_unavailable_section_areas();
				if(isset($unavailable_section_areas) && is_array($unavailable_section_areas)):?>
					<p class="mod"><strong>Pro Templates &amp; Section Areas</strong> (i.e. places to put sections)<br/>
					<?php foreach( $unavailable_section_areas as $unavailable_section_area_name ):?>
						<?php echo $unavailable_section_area_name; if($unavailable_section_area_name !== end($unavailable_section_areas)) echo ' &middot; ';?> 
					<?php endforeach;?></p>
				<?php endif;?>
				
				<p class="mod"><strong>Pro Settings &amp; Options</strong><br/>
				<?php foreach( get_option_array(true) as $optionset ):
						foreach ( $optionset as $oid => $o): 
							if( isset($o['version']) && $o['version'] == 'pro' ):
								echo $o['title']; echo ' &middot; ';
							endif;
						endforeach; 
					endforeach;?></p>
				
				<p class="mod"><strong>Plus additional meta options, integrated plugins, technical support, and more...</strong></p>
			
			</div>
		</div>
	
	<?php }

} // End Class 