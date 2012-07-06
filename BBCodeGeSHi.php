<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'bbcodegeshi_api.php' );

class BBCodeGeSHiPlugin extends MantisFormattingPlugin {
    function register() {
        $this->name = plugin_lang_get('title'); 
        $this->description = plugin_lang_get('description'); 
        $this->page = 'config';           

        $this->version = '1.0';     
        $this->requires = array(    
            'MantisCore' => '1.2.0',
            'jQuery' => '1.4.2',
            );

        $this->author = 'Vincent DEBOUT, Jiri Hron';        
        $this->contact = 'jirka.hron@gmail.com';
        $this->url = '';            
        $this->toolbar_added = false;
    }
    
	function init() {
		$t_path = config_get_global('plugin_path' ). plugin_get_current() . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;
                $geshi_path = $t_path . DIRECTORY_SEPARATOR . 'geshi' . DIRECTORY_SEPARATOR;
		set_include_path(get_include_path() . PATH_SEPARATOR . $t_path );
                set_include_path(get_include_path() . PATH_SEPARATOR . $geshi_path );
	}    
        
        /**
	 * Default plugin configuration.
	 */
	function config() {
		return array(
			'process_bbcode_text' => ON,
			'process_bbcode_email' => ON,
			'process_bbcode_rss' => ON,
		);
	}
        
        function hooks() {
                $hooks = parent::hooks();
                $new_hooks = array(
                        'EVENT_UPDATE_BUG_FORM_TOP'        => 'add_bbcode_toolbars',
                        'EVENT_REPORT_BUG_FORM_TOP'            => 'add_bbcode_toolbars',
                        'EVENT_BUGNOTE_ADD_FORM'            => 'add_bbcode_toolbars',
                        'EVENT_BUGNOTE_EDIT_FORM'            => 'add_bbcode_toolbars',
                    
                );
                return array_merge($hooks, $new_hooks);
        }
    
        /**
	 * Formatted text processing.
	 * @param string Event name
	 * @param string Unformatted text
	 * @param boolean Multiline text
	 * @return multi Array with formatted text and multiline paramater
	 */
	function formatted( $p_event, $p_string, $p_multiline = true ) {
            $t_string = $p_string;
            if ($p_multiline && (ON == plugin_config_get( 'process_bbcode_text' ))){
                $t_string = plugin_bbcodegeshi_string_display_links($t_string);
            }
            return $t_string;
	}
        
        function add_bbcode_toolbars($p_event){
            if (!$this->toolbar_added){
                plugin_bbcodegeshi_patch_textarea();
                $this->toolbar_added = true;
            }
        }
        
        /**
	 * RSS text processing.
	 * @param string Event name
	 * @param string Unformatted text
	 * @return string Formatted text
	 */
	function rss( $p_event, $p_string ) {
		$t_string = $p_string;

		if( ON == plugin_config_get( 'process_bbcode_rss' ) ) {
			$t_string = plugin_bbcodegeshi_string_display_links( $t_string );
		}

		return $t_string;
	}
        
        /**
	 * Email text processing.
	 * @param string Event name
	 * @param string Unformatted text
	 * @return string Formatted text
	 */
	function email( $p_event, $p_string ) {
		$t_string = $p_string;
                if( ON == plugin_config_get( 'process_bbcode_email' ) ) {
                    $t_string = plugin_bbcodegeshi_string_display_links( $t_string );
                }
		return $t_string;
	}
        
}