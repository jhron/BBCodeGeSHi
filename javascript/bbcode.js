/**
 * BBCodeGeSHi Plugin
 *
 *
 * Created: 2007-06-11
 * Last update: 2012-08-06
 *
 * @author Vincent DEBOUT <deboutv@free.fr>
 * @author Jiri Hron <jirka.hron@gmail.com>
 */


function AddBBCodeToolsBar( tools, descriptions ) {
  var textareas, textarea;
  var a_obj, div_obj, img_obj, parent_obj;
  textareas = GetTextarea();
  for( var i=0; i<textareas.length; i++ ) {
    textarea = document.getElementById( textareas[i] );
    parent_obj = textarea.parentNode;
    div_obj = document.createElement( 'div' );
    div_obj.id = 'bbcode-menu-' + i;
    parent_obj.insertBefore( div_obj, textarea );
    for( var j=0; j<tools.length; j++ ) {
      a_obj = document.createElement( 'a' );
      a_obj.id = 'bbcode-menu-a-' + i + '_' + tools[j];
      a_obj.href = 'javascript:UpdateTextarea( \'' + textareas[i] + '\', \'' + tools[j] + '\' );';
      a_obj.alt = descriptions[j];
      a_obj.title = descriptions[j];
      img_obj = document.createElement( 'img' );
      img_obj.id = 'bbcode-menu-img-' + i + '_' + tools[j];
      img_obj.src = 'plugins/BBCodeGeSHi/images/' + tools[j] + '.png';
      img_obj.style.padding = '2px';
      img_obj.style.border = '0px';
      img_obj.alt = tools[j];
      a_obj.appendChild( img_obj );
      div_obj.appendChild( a_obj );
    }
  }
}

function UpdateTextarea( id, tool ) {
  var pre = '', post = '', prt;
  textarea = document.getElementById( id );
  pre = '[' + tool.substring( 0, 1 ) + ']';
  post = '[/' + tool.substring( 0, 1 ) + ']';
  if ( tool == 'code' ) {
    pre = '[code';
    prt = prompt( 'Language', '' );
    if ( prt == null || prt == '' ) {
      pre = pre + ']';
    } else {
      pre = pre + '=' + prt + ']';
    }
    post = '[/code]';
  } else if ( tool == 'color' ) {
    pre = '[color';
    prt = prompt( 'Color', '' );
    if ( prt == null || prt == '' ) {
      pre = pre + ']';
    } else {
      pre = pre + '=' + prt + ']';
    }
    post = '[/color]';
  } else if ( tool == 'hr' ) {
    pre = '\n[hr]\n';
    post = '';
  } else if ( tool == 'sup' || tool == 'sub' || tool == 'left' || tool == 'center' || tool == 'right' || tool == 'justify' ) {
    pre = '[' + tool + ']';
    post = '[/' + tool + ']';
  } else if ( tool == 'bullets' ) {
    pre = '[list]\n[*] ';
    post = '\n[/list]';
  } else if ( tool == 'numbers' ) {
    pre = '[list=1]\n[*] ';
    post = '\n[/list]';
  } else if ( tool == 'size' ) {
    prt = prompt( 'Size (in px)', '' );
    if ( prt != null && prt != '' ) {
      pre = '[size=' + prt + ']';
      post = '[/size]';
    } else {
      pre = '';
      post = '';
    }
  }

  if ( textarea.setSelectionRange ) {
    var pretext = textarea.value.substring( 0, textarea.selectionStart );
    var text = textarea.value.substring( textarea.selectionStart, textarea.selectionEnd );
    var posttext = textarea.value.substring( textarea.selectionEnd, textarea.value.length );

    if ( text.length > 0 ) {
      if ( tool == 'numbers' || tool == 'bullets' ) {
	var temp = new Array();
	temp = text.split( '\n' );
	if ( temp.length > 1 ) {
	  text = temp[0] + '\n';
	  for( var i=1; i<temp.length; i++ ) {
	    text = text + '[*] ' + temp[i];
	    if ( i < temp.length - 1 ) {
	      text = text + '\n';
	    }
	  }
	}
      } else if ( tool == 'url' ) {
	var copy = text.toLowerCase();
	if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' && copy.substring( 0, 6 ) != 'ftp://' && copy.substring( 0, 7 ) != 'mailto:' ) {
	  prt = prompt( 'URL', 'http://' );
	  if ( prt == null || prt == '' || prt == 'http://' ) {
	    pre = '';
	    post = '';
	  } else {
	    copy = prt.toLowerCase();
	    if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' && copy.substring( 0, 6 ) != 'ftp://' && copy.substring( 0, 7 ) != 'mailto:' ) {
	      pre = '';
	      post = '';
	    } else {
	      pre = '[url=' + prt + ']';
	      post = '[/url]';
	    }
	  }
	} else {
	  var temp = new Array();
	  temp = text.split( ' ' );
	  if ( temp.length == 1 ) {
	    copy = prompt( 'Description', '' );
	    if ( copy != null && copy != '' ) {
	      pre = '[url=' + text + ']';
	      text = copy;
	    } else {
	      pre = '[url]';
	    }
	    post = '[/url]';
	  } else {
	    pre = '[url=' + temp[0] + ']';
	    post = '[/url]';
	    text = temp[1];
	    for( var i=2; i<temp.length; i++ ) {
	      text = text + ' ' + temp[i];
	    }
	  }
	}
      } else if ( tool == 'image' ) {
	var copy = text.toLowerCase();
	if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' ) {
	  pre = '';
	  post = '';
	} else {
	  pre = '[img]';
	  post = '[/img]';
	}
      } else if ( tool == 'email' ) {
	var copy = text.toLowerCase();
	if ( copy.indexOf( '@' ) < 1 ) {
	  prt = prompt( 'Email', '' );
	  if ( prt == null || prt.indexOf( '@' ) < 1 ) {
	    pre = '';
	    post = '';
	  } else {
	    pre = '[email=' + prt + ']';
	    post = '[/email]';
	  }
	} else {
	  var temp = new Array();
	  temp = text.split( ' ' );
	  if ( temp.length == 1 ) {
	    copy = prompt( 'Description', '' );
	    if ( copy != null && copy != '' ) {
	      pre = '[email=' + text + ']';
	      text = copy;
	    } else {
	      pre = '[email]';
	    }
	    post = '[/email]';
	  } else {
	    pre = '[email=' + temp[0] + ']';
	    post = '[/email]';
	    text = temp[1];
	    for( var i=2; i<temp.length; i++ ) {
	      text = text + ' ' + temp[i];
	    }
	  }
	}
      }
    } else {
      if ( tool == 'url' ) {
	prt = prompt( 'URL', 'http://' );
	if ( prt == null || prt == '' || prt == 'http://' ) {
	  pre = '';
	  post = '';
	} else {
	  var copy = prt.toLowerCase();
	  if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' && copy.substring( 0, 6 ) != 'ftp://' && copy.substring( 0, 7 ) != 'mailto:' ) {
	    pre = '';
	    post = '';
	  } else {
	    var desc;
	    desc = prompt( 'Description', '' );
	    if ( desc == null || desc == '' ) {
	      pre = '[url]' + prt;
	      post = '[/url]';
	    } else {
	      pre = '[url=' + prt + ']' + desc;
	      post = '[/url]';
	    }
	  }
	}
      } else if ( tool == 'image' ) {
	prt = prompt( 'URL', 'http://' );
	if ( prt == null || prt == '' || prt == 'http://' ) {
	  pre = '';
	  post = '';
	} else {
	  pre = '[img]' + prt;
	  post = '[/img]';
	}
      } else if ( tool == 'email' ) {
	prt = prompt( 'Email', '' );
	if ( prt == null || prt.indexOf( '@' ) < 1 ) {
	  pre = '';
	  post = '';
	} else {
	  var desc;
	  desc = prompt( 'Description', '' );
	  if ( desc == null || desc == '' ) {
	    pre = '[email]' + prt;
	    post = '[/email]';
	  } else {
	    pre = '[email=' + prt + ']' + desc;
	      post = '[/email]';
	  }
	}
      }
    }
    textarea.value = pretext + pre + text + post + posttext;
    var cursorpos = pretext.length + pre.length;
    if ( text.length != 0 ) cursorpos += text.length + post.length;
    textarea.setSelectionRange( cursorpos, cursorpos );
    textarea.focus();
  } else if ( document.selection ) {
    textarea.focus();
    var range = document.selection.createRange();
    var text = range.text;

    if ( text.length > 0 ) {
      if ( tool == 'numbers' || tool == 'bullets' ) {
	var temp = new Array();
	temp = text.split( '\n' );
	if ( temp.length > 1 ) {
	  text = temp[0] + '\n';
	  for( var i=1; i<temp.length; i++ ) {
	    text = text + '[*] ' + temp[i];
	    if ( i < temp.length - 1 ) {
	      text = text + '\n';
	    }
	  }
	}
      } else if ( tool == 'url' ) {
	var copy = text.toLowerCase();
	if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' && copy.substring( 0, 6 ) != 'ftp://' && copy.substring( 0, 7 ) != 'mailto:' ) {
	  prt = prompt( 'URL', 'http://' );
	  if ( prt == null || prt == '' || prt == 'http://' ) {
	    pre = '';
	    post = '';
	  } else {
	    copy = prt.toLowerCase();
	    if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' && copy.substring( 0, 6 ) != 'ftp://' && copy.substring( 0, 7 ) != 'mailto:' ) {
	      pre = '';
	      post = '';
	    } else {
	      pre = '[url=' + prt + ']';
	      post = '[/url]';
	    }
	  }
	} else {
	  var temp = new Array();
	  temp = text.split( ' ' );
	  if ( temp.length == 1 ) {
	    copy = prompt( 'Description', '' );
	    if ( copy != null && copy != '' ) {
	      pre = '[url=' + text + ']';
	      text = copy;
	    } else {
	      pre = '[url]';
	    }
	    post = '[/url]';
	  } else {
	    pre = '[url=' + temp[0] + ']';
	    post = '[/url]';
	    text = temp[1];
	    for( var i=2; i<temp.length; i++ ) {
	      text = text + ' ' + temp[i];
	    }
	  }
	}
      } else if ( tool == 'image' ) {
	var copy = text.toLowerCase();
	if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' ) {
	  pre = '';
	  post = '';
	} else {
	  pre = '[img]';
	  post = '[/img]';
	}
      } else if ( tool == 'email' ) {
	var copy = text.toLowerCase();
	if ( copy.indexOf( '@' ) < 1 ) {
	  prt = prompt( 'Email', '' );
	  if ( prt == null || prt.indexOf( '@' ) < 1 ) {
	    pre = '';
	    post = '';
	  } else {
	    pre = '[email=' + prt + ']';
	    post = '[/email]';
	  }
	} else {
	  var temp = new Array();
	  temp = text.split( ' ' );
	  if ( temp.length == 1 ) {
	    copy = prompt( 'Description', '' );
	    if ( copy != null && copy != '' ) {
	      pre = '[email=' + text + ']';
	      text = copy;
	    } else {
	      pre = '[email]';
	    }
	    post = '[/email]';
	  } else {
	    pre = '[email=' + temp[0] + ']';
	    post = '[/email]';
	    text = temp[1];
	    for( var i=2; i<temp.length; i++ ) {
	      text = text + ' ' + temp[i];
	    }
	  }
	}
      }
    } else {
      if ( tool == 'url' ) {
	prt = prompt( 'URL', 'http://' );
	if ( prt == null || prt == '' || prt == 'http://' ) {
	  pre = '';
	  post = '';
	} else {
	  var copy = prt.toLowerCase();
	  if ( copy.substring( 0, 7 ) != 'http://' && copy.substring( 0, 8 ) != 'https://' && copy.substring( 0, 6 ) != 'ftp://' && copy.substring( 0, 7 ) != 'mailto:' ) {
	    pre = '';
	    post = '';
	  } else {
	    var desc;
	    desc = prompt( 'Description', '' );
	    if ( desc == null || desc == '' ) {
	      pre = '[url]' + prt;
	      post = '[/url]';
	    } else {
	      pre = '[url=' + prt + ']' + desc;
	      post = '[/url]';
	    }
	  }
	}
      } else if ( tool == 'image' ) {
	prt = prompt( 'URL', 'http://' );
	if ( prt == null || prt == '' || prt == 'http://' ) {
	  pre = '';
	  post = '';
	} else {
	  pre = '[img]' + prt;
	  post = '[/img]';
	}
      } else if ( tool == 'email' ) {
	prt = prompt( 'Email', '' );
	if ( prt == null || prt.indexOf( '@' ) < 1 ) {
	  pre = '';
	  post = '';
	} else {
	  var desc;
	  desc = prompt( 'Description', '' );
	  if ( desc == null || desc == '' ) {
	    pre = '[email]' + prt;
	    post = '[/email]';
	  } else {
	    pre = '[email=' + prt + ']' + desc;
	      post = '[/email]';
	  }
	}
      }
    }
    if ( text.length <= 0 ) {
      range.text = pre + post;
      range.moveStart( "character", -( post.length ) );
      range.moveEnd( "character", -( post.length ) );
      range.select();
    } else {
      range.text = pre + text + post;
      range.select();
    }
  } else {
    if ( tool == 'url' || tool == 'email' ) {
      pre = '[' + tool + ']';
      post = '[/' + tool + ']';
    } else if ( tool == 'image' ) {
      pre = '[img]';
      post = '[/img]';
    }
    textarea.value = textarea.value + pre + post;
    textarea.focus();
  }
}

function GetTextarea() {
  var forms = document.forms;
  var result = new Array();
  var k = 0;
  for( var i=0; i<forms.length; i++ ) {
    if ( typeof( forms[i].action ) != 'object' ) {
      if ( forms[i].action.indexOf( 'news_add.php' ) != -1 || forms[i].action.indexOf( 'news_update.php' ) != -1 || forms[i].action.indexOf( 'bug_update.php' ) != -1 || forms[i].action.indexOf( 'bug_report.php' ) != -1 || forms[i].action.indexOf( 'bugnote_add.php' ) != -1 || forms[i].action.indexOf( 'bugnote_update.php' ) != -1 ) {
	for( var j=0; j<forms[i].elements.length; j++ ) {
	  if ( forms[i].elements[j].type == 'textarea' ) {
	    var res = document.getElementsByName( forms[i].elements[j].name );
	    if ( res.length == 1 ) {
	      if ( res[0].id == '' ) {
		res[0].id = 'bbcode_' + k;
		result.push( 'bbcode_' + k );
		k++;
	      } else {
		result.push( res[0].id );
	      }
	    }
	  }
	}
      }
    }
  }
  return result;
}
