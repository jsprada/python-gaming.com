<?php





function coolcode_info()

{

    return array(

        'name'          => 'Cool Codes',

        'description'   => 'Syntax Highlighting plus better looking code box.',

        'website'       => 'http://mybbhacks.zingaburga.com/',

        'author'        => 'ZiNgA BuRgA',

        'authorsite'    => 'http://zingaburga.com/',

        'version'       => '1.0.1'

    );

}





function coolcode_run(&$message, &$coolcode_matches, $allow_mycode = 'yes')

{

    if($allow_mycode == 'no' || empty($coolcode_matches)) return;

    global $lang;

    $langvars = array(

        'c' => 'C Code',

        'cpp' => 'C++ Code',

        'java' => 'Java Code',

        'js' => 'Javascript Code',

        'bat' => 'Batch Script',

        'sql' => 'SQL Code',

        'vb' => 'Visual Basic Code',

        'html' => 'HTML Code',

        'xml' => 'XML Code',

        'php' => $lang->php_code,

        'ini' => 'INI',

        'code' => $lang->code

    );

    require_once MYBB_ROOT.'inc/highlighter.php';

    $codes = array();

    foreach($coolcode_matches as $text)

    {

        $code = trim(strtr($text[3], array("\x00" => '', "\x01" => '')), "\n\r");

        /*

        $lines = substr_count($code, "\n");

        if(!isset($text[2]{1}))

            $start = 1;

        else

        {

            $start = intval(substr($text[2],1));

            if($start < 1) $start = 1;

        }

        

        $linetext = implode("\n", range($start, $start+$lines));

        */

        $text[1] = strtolower($text[1]);

        if($text[1] != 'code')

        {

            $func = $text[1].'_highlight';

            

            // disallow use of our special characters

            $code .= "\n";

            $code = str_replace(get_placeholder(8), 'class', substr($func($code), 0, -1)); // add & strip extra newline

        }

        else

            $code = htmlspecialchars_uni($code);

        /*

        $codes[] = "</p>\n<div class=\"code_header\">{$langvars[$text[1]]}\n</div>

            <div class=\"code_body\" style=\"padding: 0; margin: 0;\">

            <table border=\"0\" cellspacing=\"0\" cellpadding=\"1\" style=\"padding: 0;\">

            <tr><td style=\"text-align: right; width: 4em;\"><div style=\"font-family: courier new, monospaced; line-height: 1.2em;\"><pre style=\"margin: 0;\">$linetext</pre></div></td>

            <td><div style=\"font-family: courier new, monospaced; line-height: 1.2em;\"><pre style=\"margin: 0;\">$code</pre></div></td></tr></table></div>\n<p>\n";*/

        $codes[] = "</p>\n<div><div class=\"code_header\"><input type=\"button\" class=\"button\" style=\"float: right; margin-top: -2px;\" value=\"Select\" onClick=\"selectObj(this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('pre')[0])\" />{$langvars[$text[1]]}\n</div>

            <div class=\"code_body\" style=\"line-height: 1.25em;\">

            <pre>$code</pre></div></div>\n<p>\n";

        //$message = preg_replace("#\x00#", $code, $message, 1);

    }

    $message = our_str_replace("\x01", $codes, $message);

}





define('COOLCODE_JS_ADD', '<script language="javascript" type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/highlight.js"></script><link rel="stylesheet" type="text/css" href="{$mybb->settings[\'bburl\']}/css/highlight.css" />');



function coolcode_activate()

{

    require_once MYBB_ROOT."inc/adminfunctions_templates.php";

    find_replace_templatesets('headerinclude', '#$#', COOLCODE_JS_ADD);

}



function coolcode_deactivate()

{

    require_once MYBB_ROOT."inc/adminfunctions_templates.php";

    find_replace_templatesets('headerinclude', '#'.preg_quote(TOPICCOUNT_PLACEHOLDER).'#', '', 0);

}





?>
