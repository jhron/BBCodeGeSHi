<?php

/**
 * BBCodeGeSHhi Plugin
 *
 *
 * Created: 2007-06-11
 * Last update: 2012-07-06
 *
 * @link http://deboutv.free.fr/mantis/
 * @author Vincent DEBOUT <deboutv@free.fr>
 * @author Jiri Hron <jirka.hron@gmail.com> 
 */

function plugin_bbcodegeshi_string_display_links( $p_string ) {    
    if (plugin_is_installed('MantisCoreFormatting') && (ON == config_get('plugin_MantisCoreFormatting_process_urls', OFF))){        
        //fix url of url processed by mantisCoreFormating
        $p_string = preg_replace( '/\[url=<a href="(.*)".*\^<\/A>\]\](.*\[\/img\]\[\/url\]" target="_blank">\^<\/a>\])/iU', "[url=$1]$2[/url]", $p_string );
        $p_string = preg_replace( '/\[url=<a href="(.*)".*<\/a\>\]\](.*)\[\/url\]/iU', "[url=$1]$2[/url]", $p_string );
        $p_string = preg_replace( '/\[url\]<a href="(.*)\[\/url\].*\^<\/a>\]/iU', "[url]$1[/url]", $p_string );
        //fix url of img processed by MantisCoreFormating
        $p_string = preg_replace( '/\[img\]<a href="(.*)\[\/img\].*<\/a\>\]/iU', "[img]$1[/img]", $p_string );        
        //fix url of email prcoessed by mantisCoreFormating
        $p_string = preg_replace( '/\[<a href="mailto:email=(.*)">.*<\/a>\](.*)\[[\/]email]/iU', "[email=$1]$2[/email]", $p_string );
        $p_string = preg_replace( '/\[email]<a href="mailto:(.*)".*\[\/[e]mail]/iU', "[email]$1[/email]", $p_string );
    }     
    $p_string = preg_replace( "/^((http|https|ftp):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%#]+)/i", "[url]$1[/url]", $p_string );
    $p_string = preg_replace( "/([^='\"(\[url\]|\[img\])])((http|https|ftp):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%#]+)/i", "$1[url]$2[/url]", $p_string );
    $p_string = str_replace('&#160;', ' ', $p_string);
    $t_tag = config_get( 'bug_link_tag' );
    if ( '' != $t_tag ) {
        preg_match_all( '/(^|.+?)(?:(?<=^|\W)' . preg_quote( $t_tag, '/' ) . '(\d+)|$)/s', $p_string, $t_matches, PREG_SET_ORDER );
        $t_result = '';
        foreach ( $t_matches as $t_match ) {
            $t_result .= $t_match[1];
            if ( preg_match( '/\[color=$/i', $t_match[1] ) ) {
                $t_result .= $t_tag . $t_match[2];
            } else {
                if ( isset( $t_match[2] ) ) {
                    $t_bug_id = $t_match[2];
                    if ( bug_exists( $t_bug_id ) ) {
                        $t_result .= '[url=' . config_get( 'path' ) . 'view.php?id=' . $t_bug_id . ']' . bug_format_id( $t_bug_id ) . '[/url]';
                    } else {
                        $t_result .= $t_bug_id;
                    }
                }
            }
        }
        $p_string = $t_result;
    }
    $t_tag = config_get( 'bugnote_link_tag' );
    if ( '' != $t_tag ) {
        preg_match_all( '/(^|.+?)(?:(?<=^|\W)' . preg_quote( $t_tag ) . '(\d+)|$)/s', $p_string, $t_matches, PREG_SET_ORDER );
        $t_result = '';
        foreach ( $t_matches as $t_match ) {
            $t_result .= $t_match[1];
            if ( preg_match( '/\[color=$/i', $t_match[1] ) ) {
                $t_result .= $t_tag . $t_match[2];
            } else {
                if ( isset( $t_match[2] ) ) {
                    $t_bugnote_id = $t_match[2];
                    if ( bugnote_exists( $t_bugnote_id ) ) {
                        $t_bug_id = bugnote_get_field( $t_bugnote_id, 'bug_id' );
                        if ( bug_exists( $t_bug_id ) ) {
                            $t_result .= '[url=' . config_get( 'path' ) . 'view.php?id=' . $t_bug_id . '#' . $t_bugnote_id . ']' . lang_get( 'bugnote' ) . ': ' . bugnote_format_id( $t_bugnote_id ) . '[/url]';
                        } else {
                            $t_result .= $t_bugnote_id;
                        }
                    } else {
                        $t_result .= $t_bugnote_id;
                    }
                }
            }
        }
        $p_string = $t_result;
    }
    $t_cvs_web = config_get( 'cvs_web' );
    $t_path = config_get( 'path' );
    $t_replace_with = '[CVS] [url=' . $t_cvs_web . '\\1?rev=\\4"]\\1[/url]\\5';
    $p_string = preg_replace( '/cvs:([^\.\s:,\?!<]+(\.[^\.\s:,\?!<]+)*)(:)?(\d\.[\d\.]+)?([\W\s])?/i', $t_replace_with, $p_string );
    $t_extra_link_tags = 'target="_blank"';
    $t_search = array(
                        "/\[img\]((http|https|ftp):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%# ]+?)\[\/img\]/is",
                        "/\[img\]([.]*[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%# ]+?)\[\/img\]/is",
                        "/\[url\]((http|https|ftp|mailto):\/\/([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~ ]*?)\[\/url\]/is",
                        "/\[url=((http|https|ftp|mailto):\/\/[^\]]+?)\](.+?)\[\/url\]/is",
                        "/\[url=([a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%# ]+?)\](.+?)\[\/url\]/is",
                        "/\[email\]([a-z0-9\-_\.\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+?)\[\/email\]/is",
                        "/\[email=([a-z0-9\-_\.\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+?)\](.+?)\[\/email\]/is",
                        "/\[color=([\#a-z0-9]+?)\](.+?)\[\/color\]/is",
                        "/\[size=([+\-\da-z]+?)\](.+?)\[\/size\]/is",
                        "/\[list\]/is",
                        "/\[list=(.+?)\]/is",
                        "/\[\/list\]/is",
                        "/\[\*\]/is",
                        "/\[b\](.+?)\[\/b\]/is",
                        "/\[u\](.+?)\[\/u\]/is",
                        "/\[i\](.+?)\[\/i\]/is",
                        "/\[s\](.+?)\[\/s\]/is",
                        "/\[left\](.+?)\[\/left\]/is",
                        "/\[center\](.+?)\[\/center\]/is",
                        "/\[right\](.+?)\[\/right\]/is",
                        "/\[justify\](.+?)\[\/justify\]/is",
                        "/\[hr\]/i",
                        "/\[code\](.+?)\[\/code\]/ies",
                        "/\[code=([\#A-z0-9]+?)\](.+?)\[\/code\]/ies",
                        "/\[sub\](.+?)\[\/sub\]/is",
                        "/\[sup\](.+?)\[\/sup\]/is" );
    $t_replace = array(
                        "<img src=\"$1\" border=\"0\" alt=\"$1\" />",
                        "<img src=\"$1\" border=\"0\" alt=\"$1\" />",
                        "<a $t_extra_link_tags href=\"$1\">$1</a>",
                        "<a $t_extra_link_tags href=\"$1\">$3</a>",
                        "<a $t_extra_link_tags href=\"$t_path$1\">$2</a>",
                        "<a $t_extra_link_tags href=\"mailto:$1\">$1</a>",
                        "<a $t_extra_link_tags href=\"mailto:$1\">$2</a>",
                        "<span style=\"color: $1\">$2</span>",
                        "<span style=\"font-size: $1px\">$2</span>",
                        "<ol type=\"square\">",
                        "<ol type=\"$1\">",
                        "</ol>",
                        "<li>",
                        "<strong>$1</strong>",
                        "<u>$1</u>", "<i>$1</i>",
                        "<s>$1</s>",
                        "<div align=\"left\">$1</div>",
                        "<div align=\"center\">$1</div>",
                        "<div align=\"right\">$1</div>",
                        "<div align=\"justify\">$1</div>",
                        "<hr/>",
                        "plugin_bbcodegeshi_highlight_code( 'text', '$1' )",
                        "plugin_bbcodegeshi_highlight_code( '$1', '$2' )",
                        "<sub>$1</sub>",
                        "<sup>$1</sup>" );
    $t_custom_tags = config_get( 'plugin_bbcodegeshi_custom_tags', array() );
    for( $i=0; $i<count( $t_custom_tags ); $i++ ) {
        if ( $t_custom_tags[$i]['type'] == 0 ) {
            $t_search[] = '/\[' . $t_custom_tags[$i]['tag'] . '\]/i';
            $t_replace[] = $t_custom_tags[$i]['replace_by'];
        } elseif ( $t_custom_tags[$i]['type'] == 1 ) {
            $t_search[] = '/\[' . $t_custom_tags[$i]['tag'] . '\](.+?)\[\/' . $t_custom_tags[$i]['tag'] . '\]/is';
            $t_replace[] = str_replace( '%1', '$1', $t_custom_tags[$i]['replace_by'] );
        } else {
            $t_search[] = '/\[' . $t_custom_tags[$i]['tag'] . '=([^\]]+?)\](.+?)\[\/' . $t_custom_tags[$i]['tag'] . '\]/is';
            $t_replace[] = str_replace( '%2', '$2', str_replace( '%1', '$1', $t_custom_tags[$i]['replace_by'] ) );
        }
    }
    $p_string = preg_replace( $t_search, $t_replace, $p_string );
    preg_match_all( "/<pre[^>]*?>(.|\n)*?<\/pre>[(<br \/>)]*/", $p_string, $t_matches );
    $t_res = array();
    for( $x=0; $x<count( $t_matches[0] ); $x++ ) {
        $t_res[$x] = preg_replace( "/<br[^>]*?>/", '', $t_matches[0][$x] );
        $t_res[$x] = preg_replace( "/&nbsp;/", ' ', $t_res[$x] );
        $t_matches[0][$x] = '/' . preg_quote( $t_matches[0][$x], '/' ) . '/';
    }
    $p_string = preg_replace( $t_matches[0], $t_res, $p_string );
    preg_match_all( "/<ul[^>]*?>(.|\n)*?<\/ul>[(<br \/>)]*/", $p_string, $t_matches );
    $t_res = array();
    for( $x=0; $x<count( $t_matches[0] ); $x++ ) {
        $t_res[$x] = preg_replace( "/<br[^>]*?>/", '', $t_matches[0][$x] );
        $t_matches[0][$x] = '/' . preg_quote( $t_matches[0][$x], '/' ) . '/';
    }
    $p_string = preg_replace( $t_matches[0], $t_res, $p_string );
    $p_string = preg_replace( '/(<\/div>|<hr>)+?<br \/>/', '$1', $p_string );
    return $p_string;
}

function plugin_bbcodegeshi_highlight_code( $p_language, $p_string ) {
    if ( !class_exists( 'GeSHi' ) ) {
        require_once( 'geshi.php' );
    }
    $p_string = str_replace( "\r\n", "\n", $p_string );
    $p_string = str_replace( "\r", "\n", $p_string );
    $t_result = preg_replace( '/^(.*)<br \/\>$/m', '$1', $p_string );
    $t_result = str_replace( '\"', '"', $t_result );
    $t_result = str_replace( '&quot;', '"', $t_result );
    $t_result = html_entity_decode( $t_result, ENT_COMPAT );
    if ( strtolower( $p_language ) == 'html' ) {
        $p_language = 'html4strict';
    }
    $t_geshi = & new GeSHi( $t_result, $p_language );
    $t_geshi->set_header_type(GESHI_HEADER_DIV);
    $t_result = $t_geshi->parse_code();
    return $t_result;
}

function plugin_bbcodegeshi_patch_textarea() {
    $t_tools = array( 'bold', 'italic', 'strike', 'underline', 'color', 'size', 'sup', 'sub', 'bullets', 'numbers', 'code', 'hr', 'left', 'center', 'right', 'justify', 'url', 'email', 'image' );
    echo "\n" . '    <script type="text/javascript" src="plugins/BBCodeGeSHi/javascript/bbcode.js"></script>' . "\n";
    echo '    <script type="text/javascript"><!-- ' . "\n" . 'jQuery(function(){AddBBCodeToolsBar( Array( ';
    $t_first = true;
    foreach( $t_tools as $t_tool ) {
        if ( $t_first ) {
            $t_first = false;
        } else {
            echo ', ';
        }
        echo '\'' . $t_tool . '\'';
    }
    echo ' ), Array( ';
    $t_first = true;
    foreach( $t_tools as $t_tool ) {
        if ( $t_first ) {
            $t_first = false;
        } else {
            echo ', ';
        }
        echo '\'' . plugin_lang_get( 'menu_' . $t_tool ) . '\'';
    }
    echo ' ) );' . "\n" . '});' . "\n" . '      --></script>' . "\n  ";
}

?>
