<?php
/*
Plugin Name: Zestimate
Plugin URI: http://www.bbqiguana.com/
Description: Fetch a Zillow "Zestimate" for an address
Author: Randy Hunt
Version: 1.0
Author URI: http://www.bbqiguana.com/
*/

function zestimate_shortcode ( $attrs ) {
  extract( shortcode_atts( array( 'callback' => ''), $attrs) );
  $cb = '';
  if ($callback) $cb = "\t\t" . $callback . "(json.value)\n";

  $dir = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
    global $hoverswap_parent, $hoverswap_elem;
    $result = <<<EOF
    <input type="text" id="zaddress"><button id="zfind">Get Zestimate</button>
<script type="text/javascript">
//zestimate
function getZestimate() {
    var adr = escape(jQuery('#zaddress').val().replace(/ /g,'+'));
    jQuery.ajax({
        url: '$dir/zapi.php',
        data: {'address':adr},
        success: function(msg){
        var json = eval('('+msg+')');
        if (json.address) {
            alert('found:\\n' + json.address);
            $cb
        } else 
            alert(json.error);
        },
        error:function(msg){ alert('the request failed'); }
    });
}
jQuery(function(){
  jQuery('#zfind').bind('click', getZestimate);
});
</script>
EOF;
  return $result;
}

if (!is_admin()) {
    //add_action('wp_head', 'zestimate_addscript');
    add_shortcode('zestimate','zestimate_shortcode');
}

?>
