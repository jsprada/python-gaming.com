<?php

/*****************************************************************************

 *   Syntax Highlighter Core

 *     - for MyBB 1.2

 *    Copyright © 2008 ZiNgA BuRgA

 * 

 * The code which does all the syntax highlighting.

 *****************************************************************************/















/*****************

 * NOTE, when using eval'd expressions for blocks, place the 'e' modifier at the END of the string!

 ****************/

define('GENERAL_BACKSLASH_ESC_PREG', '(|\\\\\\\\|.*?([^\\\\]|[^\\\\](\\\\\\\\)+))');





function js_highlight($code)

{

    return generic_c_highlight($code, array('abstract','break','case','catch','const','continue','debugger','default','delete','do','else','export','extends','final','finally','for','function','goto','if','implements','import','in','instanceof','interface','native','new','package','return','super','switch','synchronized','this','throw','throws','try','typeof','while','with','true','false','prototype'), array('boolean','byte','char','class','doubl','enum','float','int','long','private','protected','public','short','static','transient','var','void','volatile'),

        array(array(

            'pattern' => "#([=\(]\s*)/(.*?)".GENERAL_BACKSLASH_ESC_PREG."/([a-z]*)#e",

            'replacement' => '\'<span class="js_regex">/\'.htmlspecialchars(\'$2$3\').\'/$6</span>\'',

            'keepprefix' => 1

        )), false);

}



function java_highlight($code)

{

    return generic_c_highlight($code,

    array('instanceof','assert','if','else','switch','case','default','break','goto','return','for','while','do','continue','new','throw','throws','try','catch','finally','this','super','extends','implements','import','true','false','null'),

    array('package','transient','strictfp','void','char','short','int','long','double','float','const','static','volatile','byte','boolean','class','interface','native','private','protected','public','final','abstract','synchronized','enum'));

}



function cpp_highlight($code, $c_only = false)

{

    $keyword_r = array('void','struct','union','enum','char','short','int','long','double','float','signed','unsigned','const','static','extern','auto','register','volatile');

    $inst_r = array('if','else','switch','case','default','break','goto','return','for','while','do','continue','typedef','sizeof','NULL');

    if(!$c_only)

    {

        array_push($keyword_r,'bool','class','private','protected','public','friend','inline','template','virtual','asm','explicit','typename');

        array_push($inst_r,'new','delete','throw','try','catch','namespace','operator','this','const_cast','static_cast','dynamic_cast','reinterpret_cast','true','false','using','typeid','and','and_eq','bitand','bitor','compl','not','not_eq','or','or_eq','xor','xor_eq');

    }

    return generic_c_highlight($code, $inst_r, $keyword_r);

}



function c_highlight($code)

{

    return cpp_highlight($code, true);

}



function generic_c_highlight(&$code, $instructions, $keywords, $extra_blocks = null, $preprocessor = true)

{

    $blocks = array(

        array(

            'pattern' => "#(['\"])".GENERAL_BACKSLASH_ESC_PREG."\\1#se",

            'replacement' => 'parse_c_string(\'$0\')'

            //'prefix' => '<span class="c_string">', 

            //'suffix' => '</span>'

        ),

        array(

            'pattern' => "#/\\*(.*?)\\*/#s",

            'prefix' => '<span class="c_blockcomment">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => "#//.*#",

            'prefix' => '<span class="c_linecomment">', 

            'suffix' => '</span>'

        ),

    );

    if($extra_blocks) $blocks = array_merge($blocks, $extra_blocks);

    

    $secondaries = array();

    if($preprocessor)

        $secondaries[] = array(

            'pattern' => '!([\n\r]|^)\s*?#.*!',

            'prefix' => '<span class="c_preprocessor">',

            'suffix' => '</span>'

        );

    

    $secondaries[] = array(

        'pattern' => '#(\W|^)(\.?[0-9][0-9a-zA-Z.]*)#i',

        'replacement' => '<span class="c_number">$2</span>',

        'keepprefix' => 1

    );

    $secondaries[] = array(

        'pattern' => '#([,\[\]\{\};=\+\-!%\^\*\(\)&<>|~])#',

        'prefix' => '<span class="c_symbol">',

        'suffix' => '</span>'

    );

    

    // we'll use a little hack to get around the "class" conflict

    $k = array(

        array(

            'prefix' => '<span '.get_placeholder(8).'="c_keyword">',

            'suffix' => '</span>',

            'case' => true,

            'keywords' => $keywords

        ),

        array(

            'prefix' => '<span '.get_placeholder(8).'="c_type">',

            'suffix' => '</span>',

            'case' => true,

            'keywords' => $instructions

        )

    );

    return generic_highlight($code, $blocks, $secondaries, array(), $k);

}



function parse_c_string($str, $classname = 'c', $extra_escape = '|x0[0-9a-fA-F]+|0[0-9]+')

{

    // fix added slashes :|

    return '<span class="'.$classname.'_string">'.preg_replace('#\\\\([a-zA-Z\\\\0-9\']|&quot;'.$extra_escape.')#', '<span class="'.$classname.'_string_escape">$0</span>', htmlspecialchars(str_replace('\\"', '"', $str))).'</span>';

}





function sql_highlight(&$code)

{

    // strip out double quote escapes

    $code = strtr($code, array('""' => get_placeholder(9), "''" => get_placeholder(10)));

    $blocks = array(

        array(

            'pattern' => "#(['\"`])(\\\\\\\\|.*?([^\\\\]|[^\\\\](\\\\\\\\)+)|)\\1#se",

            //'replacement' => 'parse_c_string(\'$0\', \'sql\', \'|&quot;&quot;|\\\'\\\'\')'

            //'pattern' => "#(['\"])(.*?([^\\\\]|(\\\\\\\\)+)|)\\1#s",

            'prefix' => '<span class="sql_string">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => "#/\\*(.*?)\\*/#s",

            'prefix' => '<span class="sql_blockcomment">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => "#--(.*?)[\n\r]#",

            'prefix' => '<span class="sql_linecomment">', 

            'suffix' => '</span>'

        ),

    );

    $secondaries = array(

        array(

            'pattern' => '#(\W|^)(\.?[0-9][0-9a-zA-Z.]*)#i',

            'replacement' => '<span class="sql_number">$2</span>',

            'keepprefix' => 1

        ),

        array(

            'pattern' => '#([,\[\]\{\};=\+\-!%\^&*\(\)<>|])#',

            'prefix' => '<span class="sql_symbol">',

            'suffix' => '</span>'

        ),

    );

    

    $kw = array(

        array(

            'prefix' => '<span '.get_placeholder(8).'="sql_keyword">',

            'suffix' => '</span>',

            'keywords' => array('abs','absolute','access','acos','add','add_months','adddate','admin','after','aggregate','all','allocate','alter','and','any','app_name','are','array','as','asc','ascii','asin','assertion','at','atan','atn2','audit','authid','authorization','autonomous_transaction','avg','before','begin','benchmark','between','bfilename','bin','binary','binary_checksum','binary_integer','bit','bit_count','bit_and','bit_or','blob','body','boolean','both','breadth','bulk','by','call','cascade','cascaded','case','cast','catalog','ceil','ceiling','char','char_base','character','charindex','chartorowid','check','checksum','checksum_agg','chr','class','clob','close','cluster','coalesce','col_length','col_name','collate','collation','collect','column','comment','commit','completion','compress','concat','concat_ws','connect','connection','constant','constraint','constraints','constructorcreate','contains','containsable','continue','conv','convert','corr','corresponding','cos','cot','count','count_big','covar_pop','covar_samp','create','cross','cube','cume_dist','current','current_date','current_path','current_role','current_time','current_timestamp','current_user','currval','cursor','cycle','data','datalength','databasepropertyex','date','date_add','date_format','date_sub','dateadd','datediff','datename','datepart','day','db_id','db_name','deallocate','dec','declare','decimal','decode','default','deferrable','deferred','degrees','delete','dense_rank','depth','deref','desc','describe','descriptor','destroy','destructor','deterministic','diagnostics','dictionary','disconnect','difference','distinct','do','domain','double','drop','dump','dynamic','each','else','elsif','empth','encode','encrypt','end','end-exec','equals','escape','every','except','exception','exclusive','exec','execute','exists','exit','exp','export_set','extends','external','extract','false','fetch','first','first_value','file','float','floor','file_id','file_name','filegroup_id','filegroup_name','filegroupproperty','fileproperty','for','forall','foreign','format','formatmessage','found','freetexttable','from','from_days','fulltextcatalog','fulltextservice','function','general','get','get_lock','getdate','getansinull','getutcdate','global','go','goto','grant','greatest','group','grouping','having','heap','hex','hextoraw','host','host_id','host_name','hour','ident_incr','ident_seed','ident_current','identified','identity','if','ifnull','ignore','immediate','in','increment','index','index_col','indexproperty','indicator','initcap','initial','initialize','initially','inner','inout','input','insert','instr','instrb','int','integer','interface','intersect','interval','into','is','is_member','is_srvrolemember','is_null','is_numeric','isdate','isnull','isolation','iterate','java','join','key','lag','language','large','last','last_day','last_value','lateral','lcase','lead','leading','least','left','len','length','lengthb','less','level','like','limit','limited','ln','lpad','local','localtime','localtimestamp','locator','lock','log','log10','long','loop','lower','ltrim','make_ref','map','match','max','maxextents','mid','min','minus','minute','mlslabel','mod','mode','modifies','modify','module','month','months_between','names','national','natural','naturaln','nchar','nclob','new','new_time','newid','next','next_day','nextval','no','noaudit','nocompress','nocopy','none','not','nowait','null','nullif','number','number_base','numeric','nvl','nvl2','object','object_id','object_name','object_property','ocirowid','oct','of','off','offline','old','on','online','only','opaque','open','operator','operation','option','or','ord','order','ordinalityorganization','others','out','outer','output','package','pad','parameter','parameters','partial','partition','path','pctfree','percent_rank','pi','pls_integer','positive','positiven','postfix','pow','power','pragma','precision','prefix','preorder','prepare','preserve','primary','prior','private','privileges','procedure','public','radians','raise','rand','range','rank','ratio_to_export','raw','rawtohex','read','reads','real','record','recursive','ref','references','referencing','reftohex','relative','release','release_lock','rename','repeat','replace','resource','restrict','result','return','returns','reverse','revoke','right','rollback','rollup','round','routine','row','row_number','rowid','rowidtochar','rowlabel','rownum','rows','rowtype','rpad','rtrim','savepoint','schema','scroll','scope','search','second','section','seddev_samp','select','separate','sequence','session','session_user','set','sets','share','sign','sin','sinh','size','smallint','some','soundex','space','specific','specifictype','sql','sqlcode','sqlerrm','sqlexception','sqlstate','sqlwarning','sqrt','start','state','statement','static','std','stddev','stdev_pop','strcmp','structure','subdate','substr','substrb','substring','substring_index','subtype','successful','sum','synonym','sys_context','sys_guid','sysdate','system_user','table','tan','tanh','temporary','terminate','than','then','time','timestamp','timezone_abbr','timezone_minute','timezone_hour','timezone_region','to','to_char','to_date','to_days','to_number','to_single_byte','trailing','transaction','translate','translation','treat','trigger','trim','true','trunc','truncate','type','ucase','uid','under','union','unique','unknown','unnest','update','upper','usage','use','user','userenv','using','validate','value','values','var_pop','var_samp','varchar','varchar2','variable','variance','varying','view','vsize','when','whenever','where','with','without','while','with','work','write','year','zone')

        )

    );

    $code = generic_highlight($code, $blocks, $secondaries, array(), $kw);

    // put escapes back in

    return strtr($code, array(get_placeholder(9) => '<span class="sql_string">&quot;&quot;</span>', get_placeholder(10) => '<span class="sql_string">\'\'</span>'));

}





function bat_highlight(&$code)

{

    $secondaries = array(

        array(

            'pattern' => "#([\n\r]|^)\s*?rem .*#",

            'prefix' => '<span class="bat_comment">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => '!([\n\r]|^)\s*?:.*!',

            'prefix' => '<span class="bat_label">',

            'suffix' => '</span>'

        )

    );

    

    $tertiaries = array(

        array(

            'pattern' => 

                array('#%([0-9]|%[0-9a-zA-Z!-\-])#', '#[*?]#'),

            'replacement' =>

                array('<span class="bat_var">$0</span>', '<span class="bat_symbol">$0</span>')

        ),

    );

    $kw = array(

        array(

            'prefix' => '<span class="bat_keyword">',

            'suffix' => '</span>',

            'keywords' => array('set','if','else','exist','errorlevel','for','in','do','break','call','chcp','cd','chdir','choice','cls','country','ctty','date','del','erase','dir','echo','exit','goto','loadfix','loadhigh','mkdir','md','move','path','pause','prompt','rename','ren','rmdir','rd','shift','time','type','ver','verify','vol','com','con','lpt','nul','defined','not','errorlevel','cmdextversion')

        ),

    );

    return generic_highlight($code, array(), $secondaries, $tertiaries, $kw);

}



function ini_highlight(&$code)

{

    $secondaries = array(

        array(

            'pattern' => "!([\n\r]|^)\s*?[;#].*!",

            'prefix' => '<span style="color: gray; font-style: italic;">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => "!([\n\r]|^)\s*?\[.*!",

            'prefix' => '<span style="color: #0000FF; font-weight: bold;">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => "!=(.*)!", // this ensures only one = per line is parsed

            'replacement' => '<span style="color: #FF0000;">=</span>$1', 

        ),

    );

    return generic_highlight($code, array(), $secondaries, array());

}





function asm_highlight(&$code)

{

    $blocks = array(

        array(

            'pattern' => "#(['\"])".GENERAL_BACKSLASH_ESC_PREG."\\1#se",

            'replacement' => 'parse_c_string(\'$0\', \'asm\')'

            //'prefix' => '<span class="asm_string">', 

            //'suffix' => '</span>'

        ),

        array(

            'pattern' => "#;.*?[\n\r]#",

            'prefix' => '<span class="asm_comment">', 

            'suffix' => '</span>'

        ),

    );

    

    $secondaries = array(

        array(

            'pattern' => '#(\W|^)(\.?[0-9][0-9a-zA-Z.]*)#i',

            'replacement' => '<span class="asm_number">$2</span>',

            'keepprefix' => 1

        ),

    );

    

    $keywords = array(

        array(

            'prefix' => '<span '.get_placeholder(8).'="asm_math">',

            'suffix' => '</span>',

            'keywords' => array('f2xm1','fabs','fadd','faddp','fbld','fbstp','fchs','fclex','fcom','fcomp','fcompp','fdecstp','fdisi','fdiv','fdivp','fdivr','fdivrp','feni','ffree','fiadd','ficom','ficomp','fidiv','fidivr','fild','fimul','fincstp','finit','fist','fistp','fisub','fisubr','fld','fld1','fldcw','fldenv','fldenvw','fldl2e','fldl2t','fldlg2','fldln2','fldpi','fldz','fmul','fmulp','fnclex','fndisi','fneni','fninit','fnop','fnsave','fnsavew','fnstcw','fnstenv','fnstenvw','fnstsw','fpatan','fprem','fptan','frndint','frstor','frstorw','fsave','fsavew','fscale','fsqrt','fst','fstcw','fstenv','fstenvw','fstp','fstsw','fsub','fsubp','fsubr','fsubrp','ftst','fwait','fxam','fxch','fxtract','fyl2x','fyl2xp1','fsetpm','fcos','fldenvd','fnsaved','fnstenvd','fprem1','frstord','fsaved','fsin','fsincos','fstenvd','fucom','fucomp','fucompp','fcomi','fcomip','ffreep','fcmovb','fcmove','fcmovbe','fcmovu','fcmovnb','fcmovne','fcmovnbe','fcmovnu')

        ),

        array(

            'prefix' => '<span '.get_placeholder(8).'="asm_instruct">',

            'suffix' => '</span>',

            'keywords' => array('aaa','aad','aam','aas','adc','add','and','call','cbw','clc','cld','cli','cmc','cmp','cmps','cmpsb','cmpsw','cwd','daa','das','dec','div','esc','hlt','idiv','imul','in','inc','int','into','iret','ja','jae','jb','jbe','jc','jcxz','je','jg','jge','jl','jle','jmp','jna','jnae','jnb','jnbe','jnc','jne','jng','jnge','jnl','jnle','jno','jnp','jns','jnz','jo','jp','jpe','jpo','js','jz','lahf','lds','lea','les','lods','lodsb','lodsw','loop','loope','loopew','loopne','loopnew','loopnz','loopnzw','loopw','loopz','loopzw','mov','movs','movsb','movsw','mul','neg','nop','not','or','out','pop','popf','push','pushf','rcl','rcr','ret','retf','retn','rol','ror','sahf','sal','sar','sbb','scas','scasb','scasw','shl','shr','stc','std','sti','stos','stosb','stosw','sub','test','wait','xchg','xlat','xlatb','xor','bound','enter','ins','insb','insw','leave','outs','outsb','outsw','popa','pusha','pushw','arpl','lar','lsl','sgdt','sidt','sldt','smsw','str','verr','verw','clts','lgdt','lidt','lldt','lmsw','ltr','bsf','bsr','bt','btc','btr','bts','cdq','cmpsd','cwde','insd','iretd','iretdf','iretf','jecxz','lfs','lgs','lodsd','loopd','looped','loopned','loopnzd','loopzd','lss','movsd','movsx','movzx','outsd','popad','popfd','pushad','pushd','pushfd','scasd','seta','setae','setb','setbe','setc','sete','setg','setge','setl','setle','setna','setnae','setnb','setnbe','setnc','setne','setng','setnge','setnl','setnle','setno','setnp','setns','setnz','seto','setp','setpe','setpo','sets','setz','shld','shrd','stosd','bswap','cmpxchg','invd','invlpg','wbinvd','xadd','lock','rep','repe','repne','repnz','repz','cflush','cpuid','emms','femms','cmovo','cmovno','cmovb','cmovc','cmovnae','cmovae','cmovnb','cmovnc','cmove','cmovz','cmovne','cmovnz','cmovbe','cmovna','cmova','cmovnbe','cmovs','cmovns','cmovp','cmovpe','cmovnp','cmovpo','cmovl','cmovnge','cmovge','cmovnl','cmovle','cmovng','cmovg','cmovnle','cmpxchg486','cmpxchg8b','loadall','loadall286','ibts','icebp','int1','int3','int01','int03','iretw','popaw','popfw','pushaw','pushfw','rdmsr','rdpmc','rdshr','rdtsc','rsdc','rsldt','rsm','rsts','salc','smi','smint','smintold','svdc','svldt','svts','syscall','sysenter','sysexit','sysret','ud0','ud1','ud2','umov','xbts','wrmsr','wrshr')

        ),

        array(

            'prefix' => '<span '.get_placeholder(8).'="asm_register">',

            'suffix' => '</span>',

            'keywords' => array('ah','al','ax','bh','bl','bp','bx','ch','cl','cr0','cr2','cr3','cr4','cs','cx','dh','di','dl','dr0','dr1','dr2','dr3','dr6','dr7','ds','dx','eax','ebp','ebx','ecx','edi','edx','es','esi','esp','rax','rbp','rbx','rcx','rdi','rdx','rsi','rsp','fs','gs','si','sp','ss','st','tr3','tr4','tr5','tr6','tr7','st0','st1','st2','st3','st4','st5','st6','st7','mm0','mm1','mm2','mm3','mm4','mm5','mm6','mm7','xmm0','xmm1','xmm2','xmm3','xmm4','xmm5','xmm6','xmm7')

        ),

        array(

            'prefix' => '<span '.get_placeholder(8).'="asm_directive">',

            'suffix' => '</span>',

            'keywords' => array('.186','.286','.286c','.286p','.287','.386','.386c','.386p','.387','.486','.486p','.8086','.8087','.alpha','.break','.code','.const','.continue','.cref','.data','.data?','.dosseg','.else','.elseif','.endif','.endw','.err','.err1','.err2','.errb','.errdef','.errdif','.errdifi','.erre','.erridn','.erridni','.errnb','.errndef','.errnz','.exit','.fardata','.fardata?','.if','.lall','.lfcond','.list','.listall','.listif','.listmacro','.listmacroall','.model','.no87','.nocref','.nolist','.nolistif','.nolistmacro','.radix','.repeat','.sall','.seq','.sfcond','.stack','.startup','.tfcond','.type','.until','.untilcxz','.while','.xall','.xcref','.xlist','alias','align','assume','catstr','comm','comment','db','dd','df','dosseg','dq','dt','dup','dw','echo','else','elseif','elseif1','elseif2','elseifb','elseifdef','elseifdif','elseifdifi','elseife','elseifidn','elseifidni','elseifnb','elseifndef','end','endif','endm','endp','ends','eq','equ','even','exitm','extern','externdef','extrn','for','forc','ge','goto','group','gt','high','highword','if','if1','if2','ifb','ifdef','ifdif','ifdifi','ife','ifidn','ifidni','ifnb','ifndef','include','includelib','instr','invoke','irp','irpc','label','le','length','lengthof','local','low','lowword','lroffset','lt','macro','mask','mod','.msfloat','name','ne','offset','opattr','option','org','%out','page','popcontext','proc','proto','ptr','public','purge','pushcontext','record','repeat','rept','seg','segment','short','size','sizeof','sizestr','struc','struct','substr','subtitle','subttl','textequ','this','title','type','typedef','union','while','width','db','dw','dd','dq','dt','resb','resw','resd','resq','rest','incbin','equ','times','%define','%idefine','%xdefine','%xidefine','%undef','%assign','%iassign','%strlen','%substr','%macro','%imacro','%endmacro','%rotate','.nolist','%if','%elif','%else','%endif','%ifdef','%ifndef','%elifdef','%elifndef','%ifmacro','%ifnmacro','%elifmacro','%elifnmacro','%ifctk','%ifnctk','%elifctk','%elifnctk','%ifidn','%ifnidn','%elifidn','%elifnidn','%ifidni','%ifnidni','%elifidni','%elifnidni','%ifid','%ifnid','%elifid','%elifnid','%ifstr','%ifnstr','%elifstr','%elifnstr','%ifnum','%ifnnum','%elifnum','%elifnnum','%error','%rep','%endrep','%exitrep','%include','%push','%pop','%repl','struct','endstruc','istruc','at','iend','align','alignb','%arg','%stacksize','%local','%line','bits','use16','use32','section','absolute','extern','global','common','cpu','org','section','group','import','export')

        ),

        array(

            'prefix' => '<span '.get_placeholder(8).'="asm_var">',

            'suffix' => '</span>',

            'keywords' => array('$','?','@b','@f','addr','basic','byte','c','carry?','dword','far','far16','fortran','fword','near','near16','overflow?','parity?','pascal','qword','real4','real8','real10','sbyte','sdword','sign?','stdcall','sword','syscall','tbyte','vararg','word','zero?','flat','near32','far32','abs','all','assumes','at','casemap','common','compact','cpu','dotname','emulator','epilogue','error','export','expr16','expr32','farstack','flat','forceframe','huge','language','large','listing','ljmp','loadds','m510','medium','memory','nearstack','nodotname','noemulator','nokeyword','noljmp','nom510','none','nonunique','nooldmacros','nooldstructs','noreadonly','noscoped','nosignextend','nothing','notpublic','oldmacros','oldstructs','os_dos','para','private','prologue','radix','readonly','req','scoped','setif2','smallstack','tiny','use16','use32','uses','a16','a32','o16','o32','byte','word','dword','nosplit','$','$$','seq','wrt','flat','large','small','.text','.data','.bss','near','far','%0','%1','%2','%3','%4','%5','%6','%7','%8','%9')

        ),

    );

    

    return generic_highlight($code, $blocks, $secondaries, array(), $keywords);

}





function php_highlight(&$code)

{

    // See if open and close tags are provided.

    if(preg_match('#^(\s*)(\<\?(php|))#i', $code, $open_tag, PREG_OFFSET_CAPTURE))

    {

        $code = substr($code, strlen($open_tag[0][0]));

        $open_tag = $open_tag[1][0].'<span class="php_tag">'.htmlspecialchars($open_tag[2][0]).'</span>';

    }

    if(preg_match('#(\?\>)(\s*)$#', $code, $end_tag, PREG_OFFSET_CAPTURE))

    {

        $code = substr($code, 0, -strlen($end_tag[0][0]));

        $end_tag = '<span class="php_tag">'.htmlspecialchars($end_tag[1][0]).'</span>'.$end_tag[2][0];

    }

    

    $blocks = array(

        array(

            'pattern' => '#\?\>(.*?)(\<\?(php()|(\s))|($()))#ise',

            'replacement' => '\'<span class="php_tag">?&gt;</span>\'.block_background(html_highlight(str_replace(\'\\\\\\\\"\', \'"\',\'$1\')), \'php_html\').\'<span class="php_tag">\'.htmlspecialchars(\'$2\').\'</span>\'',

            'keepsuffix' => 4

        ),

        array(

            'pattern' => "#'".GENERAL_BACKSLASH_ESC_PREG."'#se",

            'replacement' => 'parse_php_string_simple(\'$0\')',

            //'prefix' => '<span class="php_string">', 

            //'suffix' => '</span>'

        ),

        array(

            'pattern' => '#"'.GENERAL_BACKSLASH_ESC_PREG.'"#se',

            'replacement' => '\'<span class="php_string_d">&quot;\'.parse_php_string(\'$1\').\'&quot;</span>\''

        ),

        array(

            'pattern' => "#\<\<\<(.+?)([\n\r])(.*?)([\n\r]\\1)#se",

            'replacement' => '\'<span class="php_string_h">&lt;&lt;&lt;$1$2\'.parse_php_string(\'$3\').\'$4</span>\''

        ),

        array(

            'pattern' => "#/\\*(.*?)\\*/#s",

            'prefix' => '<span class="php_blockcomment">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => "#//(.*?)[\n\r]#",

            'prefix' => '<span class="php_linecomment">', 

            'suffix' => '</span>'

        ),

    );

    

    $secondaries = array(

        array(

            'pattern' => '#(\W|^)(\.?[0-9][0-9a-zA-Z.]*)#',

            'replacement' => '<span class="php_number">$2</span>',

            'keepprefix' => 1

        ),

        array(

            'pattern' => '#([,\[\]\{\};=\+\-!%\^&\*\(\)<>|@.~])#',

            'prefix' => '<span class="php_symbol">',

            'suffix' => '</span>'

        ),

        array(

            'pattern' => '#\$[a-zA-Z_][a-zA-Z0-9_]*#',

            'prefix' => '<span class="php_var">',

            'suffix' => '</span>'

        )

    );

    

    $kw = array(

        array(

            'prefix' => '<span '.get_placeholder(8).'="php_keyword">',

            'suffix' => '</span>',

            'keywords' => array('and','or','xor','__file__','__line__','array','as','break','case','cfunction','class','const','continue','declare','default','die','do','echo','else','elseif','empty','enddeclare','endfor','endforeach','endif','endswitch','endwhile','eval','exit','extends','for','foreach','function','global','if','include','include_once','isset','list','new','old_function','print','require','require_once','return','static','switch','unset','use','var','while','__function__','__class__','php_version','php_os','default_include_path','pear_install_dir','pear_extension_dir','php_extension_dir','php_bindir','php_libdir','php_datadir','php_sysconfdir','php_localstatedir','php_config_file_path','php_output_handler_start','php_output_handler_cont','php_output_handler_end','e_error','e_warning','e_parse','e_notice','e_core_error','e_core_warning','e_compile_error','e_compile_warning','e_user_error','e_user_warning','e_user_notice','e_all','true','false','bool','boolean','int','integer','float','double','real','string','array','object','resource','null','class','extends','parent','stdclass','directory','__sleep','__wakeup','interface','implements','abstract','public','protected','private')

        )

    );

    $code = generic_highlight($code, $blocks, $secondaries, array(), $kw);

    // put back end/start tags if necessary

    if(isset($open_tag) && is_string($open_tag))

        $code = $open_tag.$code;

    if(isset($end_tag) && is_string($end_tag))

        $code .= $end_tag;

    

    return $code;

}



function parse_php_string($str)

{

    return preg_replace(array('#'.GENERAL_BACKSLASH_ESC_PREG.'\{(\$.*?)'.GENERAL_BACKSLASH_ESC_PREG.'\}#', '#($|[^\\\\]|[^\\\\](\\\\\\\\)+)\$([a-zA-Z_][a-zA-Z0-9_]*)#'), array('$1<span class="php_string_code">{$4$5}</span>', '$1<span class="php_string_var">$$3</span>'), parse_c_string($str, 'php', '|[${}]|x0[0-9a-fA-F]+|0[0-9]+'));

//  return preg_replace(array('#'.GENERAL_BACKSLASH_ESC_PREG.'\{(\$.*?)'.GENERAL_BACKSLASH_ESC_PREG.'\}#', '#'.GENERAL_BACKSLASH_ESC_PREG.'\$([a-zA-Z_][a-zA-Z0-9_]*)#'), array('$1<span class="php_string_code">{$4$5}</span>', '$1<span class="php_string_var">$$4</span>'), htmlspecialchars(str_replace('\\"', '"', $str)));

}



function parse_php_string_simple($str)

{

    // fix added slashes :|

    return '<span class="php_string">'.preg_replace('#\\\\([\\\\\'])#', '<span class="php_string_escape">$0</span>', htmlspecialchars(str_replace('\\"', '"', $str))).'</span>';

}







function vb_highlight(&$code)

{

    // strip out double quote escapes (we're lucky that it's okay to stack <span>'s :P)

    $code = str_replace('""', get_placeholder(10), $code);

    $blocks = array(

        array(

            'pattern' => '#".+?"#',

            'prefix' => '<span class="vb_string">', 

            'suffix' => '</span>'

        ),

        array(

            'pattern' => "#'.*#",

            'prefix' => '<span class="vb_comment">', 

            'suffix' => '</span>'

        ),

    );

    

    $secondaries = array(

        array(

            'pattern' => '!([\n\r]|^)\s*?#.*!',

            'prefix' => '<span class="vb_preprocessor">',

            'suffix' => '</span>'

        ),

        array(

            'pattern' => '!(\W|^)(\.?[0-9][0-9.]*|&[hH][0-9a-fA-F]*)!',

            'replacement' => '<span class="vb_number">$2</span>',

            'keepprefix' => 1

        ),

        array(

            'pattern' => '~[,=\+\-!%\^&\*\(\)\<\>#$|]~',

            'prefix' => '<span class="vb_symbol">',

            'suffix' => '</span>'

        )

    );

    

    $kw = array(

        array(

            'prefix' => '<span '.get_placeholder(8).'="vb_keyword">',

            'suffix' => '</span>',

            'keywords' => array('addhandler','addressof','andalso','alias','and','ansi','as','assembly','attribute','auto','begin','call','case','catch','cbool','cbyte','cchar','cdate','cdec','cdbl','char','cint','class','clng','cobj','compare','const','continue','cshort','csng','cstr','ctype','declare','default','delegate','dim','do','each','else','elseif','end','erase','error','event','exit','explicit','finally','for','friend','function','get','gettype','global','gosub','goto','handles','if','implement','implements','imports','in','inherits','interface','is','let','lib','like','load','loop','lset','me','mid','mod','module','mustinherit','mustoverride','mybase','myclass','namespace','new','next','not','nothing','notinheritable','notoverridable','on','open','option','or','orelse','overloads','overridable','overrides','paramarray','preserve','property','raiseevent','readonly','redim','rem','removehandler','rset','resume','return','select','set','shadows','shared','step','stop','structure','sub','synclock','then','throw','to','try','typeof','unload','unicode','until','wend','when','while','with','withevents','writeonly','xor')

        ),

        array(

            'prefix' => '<span '.get_placeholder(8).'="vb_type">',

            'suffix' => '</span>',

            'keywords' => array('boolean','byref','byte','byval','currency','date','decimal','double','enum','false','integer','long','object','optional','private','protected','public','short','single','static','string','true','type','variant')

        ),

    );

    $code = generic_highlight($code, $blocks, $secondaries, array(), $kw);

    // put escapes back in

    return str_replace(get_placeholder(10), '<span class="vb_string">&quot;&quot;</span>', $code);

}







function html_highlight(&$code)

{

    return xml_highlight($code, true);

}



function xml_highlight($code, $html=false)

{

    $blocks = array(

        array(

            'pattern' => '#\<!--.*?--\>#s',

            'prefix' => '<span class="xml_comment">', 

            'suffix' => '</span>'

        )

    );

    if(!$html)

    {

        $blocks[] = array(

            'pattern' => '#\<!\[CDATA\[.*?\]\]\>#',

            'prefix' => '<span class="xml_cdata">', 

            'suffix' => '</span>'

        );

    }

    else // script/CSS blocks

    {

        $blocks[] = array(

            'pattern' => '#\<script(.*?)\>(.*?)\</script\>#ise',

            'replacement' => 'html_parse_script(\'$0\', \'$1\')'

        );

        $blocks[] = array(

            'pattern' => '#\<style(.*?)\>(.*?)\</style\>#ise',

            'replacement' => 'html_parse_style(\'$0\')'

        );

    }

    

    $secondaries = array(

        array(

            'pattern' => '|\</?([0-9a-zA-Z_#\-]+?)\>|',

            'prefix' => '<span class="xml_tag">',

            'suffix' => '</span>'

        ),

        array(

            'pattern' => '|\<(\??[0-9a-zA-Z_#\-]*?)\>|',

            'prefix' => '<span class="xml_tag">',

            'suffix' => '</span>'

        ),

        array(

            'pattern' => '#&([0-9a-zA-Z]+?);#',

            'prefix' => '<span class="xml_escape">',

            'suffix' => '</span>'

        ),

    );

    

    global $ishtml;

    $ishtml = $html;

    return generic_highlight($code, $blocks, $secondaries, array(), array(), 'xml_highlight_callback');

    $ishtml = null;

}





function html_parse_script($code, $prop = '')

{

    // strip ghey slashes

    $code = str_replace('\\"', '"', $code);

    $prop = str_replace('\\"', '"', $prop);

    // we can assume the <script> tag exists

    // check simple script tag first

    if(strtolower(substr($code, 0, 8)) == '<script>')

    {

        return '<span class="xml_tag">'.htmlspecialchars(substr($code, 0, 8)).'</span>'.block_background(htmlspecialchars(substr($code, 8, -9)), 'html_script').'<span class="xml_tag">'.htmlspecialchars(substr($code, -9)).'</span>';

    }

    

    $nm = xml_replace_next_tag($code, '<script');

    $end_tag = '<span class="xml_tag">'.htmlspecialchars(substr($code, -9)).'</span>';

    

    $scriptcode = substr($nm['suffix'], 0, -9);

    // parse <!-- --> if necessary

    if(preg_match('#^(\s*)\<!--(.*?)-->(\s*)$#s', $scriptcode, $comment, PREG_OFFSET_CAPTURE))

    {

        //$code = substr($code, strlen($comment[1][0]+4), -strlen($comment[3][0]+3));

        $scriptcode = $comment[2][0];

        $comment_pre = $comment[1][0].'<span class="xml_comment">&lt;!--</span>';

        $comment_post = '<span class="xml_comment">--&gt;</span>'.$comment[3][0];

    }

    

    // try to determine script type

    $parsed = false;

    if($prop)

    {

        $prop = explode(' ', $prop);

        foreach($prop as $p)

        {

            $p = strtolower($p);

            if(substr($p, 0, 5) == 'type=')

            {

                $type = substr($p, 5);

                // strip quotes

                if($type{0} == $type{strlen($type)-1} && ($type{0} == '"' || $type{0} == "'"))

                    $type = substr($type, 1, -1);

                

                // parse "text/javascript"

                if($type == 'text/javascript') $type = 'javascript';

                elseif($type = 'text/vbscript') $type = 'vbscript';

                

                break;

            }

            elseif(substr($p, 0, 9) == 'language=')

            {

                $type = substr($p, 9);

                // strip quotes

                if($type{0} == substr($type, -1) && ($type{0} == '"' || $type{0} == "'"))

                    $type = substr($type, 1, -1);

                break;

            }

        }

        if($type == 'javascript')

        {

            $parsed = true;

            $scriptcode = js_highlight($scriptcode);

        }

        if($type == 'vbscript')

        {

            $parsed = true;

            $scriptcode = vb_highlight($scriptcode);

        }

    }

    // unknown script

    if(!$parsed)

        $scriptcode = htmlspecialchars($scriptcode);

    if(isset($comment_pre) || isset($comment_post))

        $scriptcode = $comment_pre.$scriptcode.$comment_post;

    return $nm['match'].block_background($scriptcode, 'html_script').$end_tag;

}





function html_parse_style($code)

{

    // strip ghey slashes

    $code = str_replace('\\"', '"', $code);

    // we can assume the <style> tag exists

    // check simple style tag first

    if(strtolower(substr($code, 0, 7)) == '<style>')

    {

        return '<span class="xml_tag">'.htmlspecialchars(substr($code, 0, 7)).'</span>'.block_background(htmlspecialchars(substr($code, 7, -8)), 'html_style').'<span class="xml_tag">'.htmlspecialchars(substr($code, -8)).'</span>';

    }

    

    $nm = xml_replace_next_tag($code, '<style');

    $end_tag = '<span class="xml_tag">'.htmlspecialchars(substr($code, -8)).'</span>';

    

    $styletcode = substr($nm['suffix'], 0, -8);

    // parse <!-- --> if necessary

    if(preg_match('#^(\s*)\<!--(.*?)-->(\s*)$#s', $styletcode, $comment, PREG_OFFSET_CAPTURE))

    {

        //$code = substr($code, strlen($comment[1][0]+4), -strlen($comment[3][0]+3));

        $styletcode = $comment[2][0];

        $comment_pre = $comment[1][0].'<span class="xml_comment">&lt;!--</span>';

        $comment_post = '<span class="xml_comment">--&gt;</span>'.$comment[3][0];

    }

    

    $styletcode = htmlspecialchars($styletcode);

    if(isset($comment_pre) || isset($comment_post))

        $styletcode = $comment_pre.$styletcode.$comment_post;

    return $nm['match'].block_background($styletcode, 'html_style').$end_tag;

}





function xml_highlight_callback(&$code, &$smatches)

{

    global $ishtml;

    $placeholders = array(

        1 => get_placeholder(1)

    );

    

    preg_match_all('/\<((\?|!\s*)?[0-9a-zA-Z_#\-]*?) (.*?)\>/s', $code, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

    $codetemp = $code;

    $code = '';

    $offset = 0;

//  $coffset = 0;



    foreach($matches as $match)

    {

        if($match[0][1] < $offset) continue; // expired block

        $pos = $match[0][1]-$offset;

        $code .= substr($codetemp, 0, $pos);

        if(!($nm = xml_replace_next_tag(substr($codetemp, $pos), '<'.$match[1][0], $ishtml)))

            break;

        if(is_array($nm))

        {

            $smatches[0][] = $nm['match'];

            //$code .= $placeholders[1].$nm['suffix'];

            $code .= $placeholders[1];

            //$absendpos = $match[0][1] + strlen($match[0][0]);

            //$codetemp = substr($codetemp, $absendpos);

            $codetemp = $nm['suffix'];

            $offset = $match[0][1] + $nm['matchlen'];

        }

        else

            break;

    }

    $code .= $codetemp;

    unset($codetemp);

}



function xml_replace_next_tag($code, $tagprefix, $html = false)

{

    $js_attrib_array = array(

        'onmouseover' => 1,

        'onmouseout' => 1,

        'onmousedown' => 1,

        'onmousemove' => 1,

        'onmouseup' => 1,

        'onclick' => 1,

        'ondblclick' => 1,

        'onload' => 1,

        'onsubmit' => 1,

        'onblur' => 1,

        'onchange' => 1,

        'onfocus' => 1,

        'onselect' => 1,

        'onunload' => 1,

        'onkeypress' => 1,

    );

    $taglen = strlen($tagprefix);

    $code = substr($code, $taglen);

    $codebuild = '';

    $done = false;

    while(!$done)

    {

        // the first time through, $code{0} == ' '

        if(!preg_match('#\S#', $code, $match, PREG_OFFSET_CAPTURE))

        {

            // rest of code is just whitespace?

            $taglen += strlen($code);

            $codebuild .= $code;

            $code = '';

            $done = true;

            break;

        }

        $codebuild .= substr($code, 0, $match[0][1]);

        $code = substr($code, $match[0][1]);

        $taglen += $match[0][1];

        switch($code{0})

        {

            case '"':

            case "'":

                // search for ending quote

                $pos = strpos($code, $code{0}, 1);

                if($pos === false)

                {

                    // this quote doesn't end?

                    $taglen += strlen($code);

                    $codebuild .= '<span class="xml_attribvalue">'.$code.'</span>';

                    $code = '';

                    $done = true;

                    break;

                }

                else

                {

                    ++$pos; // cover the end quote

                    $codebuild .= '<span class="xml_attribvalue">'.preg_replace('#&amp;([0-9a-zA-Z]+?);#', '<span class="xml_escape">$0</span>', htmlspecialchars(substr($code, 0, $pos))).'</span>';

                    $code = substr($code, $pos);

                    $taglen += $pos;

                    break;

                }

                

            case '/':

            case '?':

                // end tag?

                if(!isset($code{1}) || $code{1} != '>')

                {// no end tag :(

                    $codebuild .= $code{0};

                    $code = substr($code, 1);

                    ++$taglen;

                    break;

                }

            case '>':

                // ooh, end tag

                if($code{0} != '>')

                {

                    $codebuild .= '<span class="xml_tag">'.$code{0}.'&gt;</span>';

                    $code = substr($code, 2);

                    $taglen += 2;

                }

                else

                {

                    $codebuild .= '<span class="xml_tag">&gt;</span>';

                    $code = substr($code, 1);

                    ++$taglen;

                }

                $done = true;

                break;

            default:

                // probably an attribute

                if(preg_match('#(\s|[=<>/?])#', $code, $match, PREG_OFFSET_CAPTURE))

                {

                    $attribname = substr($code, 0, $match[0][1]);

                    $attrib = '<span class="xml_attrib">'.htmlspecialchars($attribname).'</span>';

                    if($match[0][0] == '=') // atrribute value

                    {

                        $codebuild .= $attrib.'<span class="xml_attribequal">=</span>';

                        $code = substr($code, $match[0][1]+1);

                        $taglen += $match[0][1]+1;

                        // are there quotes?

                        if($code{0} == '"' || $code{0} == "'")

                        {

                            // search for ending quote

                            $pos = strpos($code, $code{0}, 1);

                            if($pos === false)

                            {

                                // this quote doesn't end?

                                $taglen += strlen($code);

                                $codebuild .= '<span class="xml_attribvalue">'.$code.'</span>';

                                $code = '';

                                $done = true;

                                break;

                            }

                            else

                            {

                                ++$pos; // cover the end quote

                                // check javascript

                                if($html && isset($js_attrib_array[strtolower($attribname)]))

                                {

                                    $codebuild .= '<span class="xml_attribvalue">'

                                    .htmlspecialchars($code{0}).'</span>'.block_background(js_highlight(substr($code, 1, $pos-2)), 'html_script').'<span class="xml_attribvalue">'.htmlspecialchars($code{$pos-1}).'</span>';

                                }

                                else

                                {

                                    $codebuild .= '<span class="xml_attribvalue">'.preg_replace('#&amp;([0-9a-zA-Z]+?);#', '<span class="xml_escape">$0</span>', htmlspecialchars(substr($code, 0, $pos))).'</span>';

                                }

                                $code = substr($code, $pos);

                                $taglen += $pos;

                                break;

                            }

                        }

                        else

                        {

                            // no quotes - search for end position

                            if(preg_match('#[ "$@=\'/<>]#', $code, $match, PREG_OFFSET_CAPTURE))

                            {

                                $codebuild .= '<span class="xml_attribvalue">'.htmlspecialchars(substr($code, 0, $match[0][1])).'</span>';

                                $code = substr($code, $match[0][1]);

                                $taglen += $match[0][1];

                                break;

                            }

                            else

                            {

                                // this attrib doesn't end?

                                $taglen += strlen($code);

                                $codebuild .= '<span class="xml_attribvalue">'.$code.'</span>';

                                $code = '';

                                $done = true;

                                break;

                            }

                        }

                        

                    }

                    else // single, non-valued attribute

                    {

                        $codebuild .= $attrib;

                        $code = substr($code, $match[0][1]);

                        $taglen += $match[0][1];

                    }

                }

                else

                {

                    // this attribute doesn't end?

                    $taglen += strlen($code);

                    $codebuild = '<span class="xml_attrib">'.$code.'</span>';

                    $code = '';

                    $done = true;

                    break;

                }

        }

    }

    /*if($taglen > 38)

    {

    die(var_dump(array(

        'match' => '<span class="xml_tag">'.htmlspecialchars($tagprefix).'</span>'.$codebuild,

        'matchlen' => $taglen

    )));

    }*/

    return array(

        'match' => '<span class="xml_tag">'.htmlspecialchars($tagprefix).'</span>'.$codebuild,

        'suffix' => $code,

        'matchlen' => $taglen

    );

}



/**********************************

* OLD (slow) METHOD

***********************************

// note, $code should be offsetted to the correct opening tag place

function xml_replace_next_tag($code, $tagprefix, $block_matches =null, $boffset = 0)

{

    $placeholders = array(9 => get_placeholder(9));

    if(!isset($block_matches))

    {

        $patterns = array(

            'close' => '#([\?/]|)\>#',

            'attrib' => '!([a-zA-Z0-9_#\-:]+?)=([\'"])(.*?)(\\2)!s',

            'attribnoq' => '!([a-zA-Z0-9_#\-:]+?)=([^ "\'@$-,]+?)!',

            'attribsgl' => '!([a-zA-Z0-9_#\-:]+)!',

            'quote' => '!([\'"]).*?\\1!s',

        );

        

        $code = substr($code, strlen($tagprefix));

        $block_matches = array();

        foreach($patterns as $key => $pattern)

        {

            preg_match_all($pattern, $code, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

            if(!empty($matches))

            {

                foreach($matches as $match)

                {

                    // only override current match if it's longer

                    if(!isset($block_matches[$match[0][1]]) || strlen($match[0][0]) > strlen($block_matches[$match[0][1]][1][0][0]))

                        $block_matches[$match[0][1]] = array(0 => $key, 1 => $match);

                }

            }

        }

        ksort($block_matches);

    }

    

    $pos = strpos($code, '>');

    $code = substr($code, $pos);

    return array(

        'match' => '<span class="xml_tag">'.htmlspecialchars($tagprefix).'</span>',

        'suffix' => $code,

        'matchlen' => $pos - $boffset

    );

    

    // now that we have the sorted array, perform replacements

    //$offset = 0;

    $offset = $boffset;

    $codetemp = $code;

    $code = '';

    //$count = 0;

    $rtags = array();

    $tag_closed = false;

    foreach($block_matches as $pos => $minfo)

    {

        $text = &$minfo[1][0][0];

        // is this a "passed" block?

        if($pos < $offset) continue;

        

        switch($minfo[0])

        {

            case 'close':

                $rtags[] = '<span class="xml_tag">'.htmlspecialchars($text).'</span>';

                $tag_closed = $pos + strlen($text) + strlen($tagprefix);

                break;

            case 'attrib':

                $rtags[] = '<span class="xml_attrib">'.$minfo[1][1][0].'</span><span class="xml_attribequal">=</span><span class="xml_attribvalue">'.htmlspecialchars($minfo[1][2][0].$minfo[1][3][0].$minfo[1][4][0]).'</span>';

                break;

            case 'attribnoq':

                $rtags[] = '<span class="xml_attrib">'.$minfo[1][1][0].'</span><span class="xml_attribequal">=</span><span class="xml_attribvalue">'.htmlspecialchars($minfo[1][2][0]).'</span>';

                break;

            case 'attribsgl':

                $rtags[] = '<span class="xml_attrib">'.htmlspecialchars($text).'</span>';

                break;

            case 'quote':

                $rtags[] = '<span class="xml_attribvalue">'.htmlspecialchars($text).'</span>';

                break;

        }

        

        $code .= substr($codetemp, 0, $pos-$offset).$placeholders[9];

        $len = strlen($text);

        $codetemp = substr($codetemp, $pos+$len - $offset);

        $offset = $pos + $len;

        unset($block_matches[$pos]);

        if($tag_closed) break;

    }

    //$code .= substr($codetemp, $offset);

    //unset($codetemp);

    

    if(!empty($rtags))

    {

        if($tag_closed)

        {

            //die(var_dump($oldlen-strlen($codetemp)));

            return array(

                'match' => '<span class="xml_tag">'.htmlspecialchars($tagprefix).'</span>'.our_str_replace($placeholders[9], $rtags, htmlspecialchars($code)),

                'suffix' => $codetemp,

                'matchlen' => $tag_closed - $boffset

            );

        }

        else

        {

            // if tag never closed... don't do anything, but break - $inspect will be discarded

            return false;

        }

    }

    

    die($tagprefix);

    return true;

    

    //unset($block_matches);

    

}

*/









function generic_highlight(&$code, $blocks = array(), $secondaries = array(), $tertiaries = array(), $keywords = array(), $callback = '')

{

    $placeholders = array();

    for($i=0;$i<10;$i++)

        $placeholders[$i] = get_placeholder($i);

    // this will parse out "block" codes, ie comments and quotes

    $block_matches = array();

    $replaced_blocks = array();

    //$key_map = array();

    foreach($blocks as $key => $binfo)

    {

        //$binfo['key'] = $key;

        preg_match_all($binfo['pattern'], $code, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        foreach($matches as $match)

        {

            $block_matches[$match[0][1]] = array(0 => $key, 1 => $match);

            //$key_map[$match[0][1]] = $key;

        }

    }

    ksort($block_matches);

    

    // now that we have the sorted array, perform replacements

    $offset = 0;

    $codetemp = $code;

    $code = '';

    //$count = 0;

    do {

        $redo = false;

        foreach($block_matches as $pos => $minfo)

        {

            $text = &$minfo[1][0][0];

            $binfo = &$blocks[$minfo[0]];

            // is this a "passed" block?

            if($pos < $offset)

            {

                // if the close of the block is also passed, skip the whole thing

                //  -OR there's no more matches

                if(($pos + strlen($text)) < $offset || !preg_match($binfo['pattern'], $codetemp))

                {

                    unset($block_matches[$pos]);

                    continue;

                }

                

                // otherwise, we'll have to try and re-match everything :(

                preg_match_all($binfo['pattern'], $codetemp, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

                //$code .= $codetemp;

                // remove all current blocks for this pattern

                $block_matches = array_filter($block_matches, create_function('$a', 'return $a[0]!=\''.$minfo[0].'\';'));

                //foreach($block_matches as $pos2 => $minfo2)

                //  if($minfo2[0] == $minfo[0])

                //      unset($block_matches[$pos2]);

                // re-assign matches

                foreach($matches as $match)

                    $block_matches[$match[0][1] + $offset] = array(0 => $minfo[0], 1 => $match);

                ksort($block_matches);

                // restart the whole process

                $redo = true;

                //$offset = 0;

                //$codetemp = $code;

                //$code = '';

                break;

            }

            

            if(isset($binfo['replacement']))

                $replaced_blocks[] = preg_replace($binfo['pattern'], $binfo['replacement'], $text);

            elseif(isset($binfo['soffset']) || isset($binfo['eoffset']))

                $replaced_blocks[] = offset_join_htmlspecialchars($text, $binfo['prefix'], $binfo['suffix'], $binfo['soffset'], $binfo['eoffset']);

            else

                $replaced_blocks[] = $binfo['prefix'].htmlspecialchars($text).$binfo['suffix'];

            

            $ph = $placeholders[0];

            if(isset($binfo['keepprefix']))

                $ph = $minfo[1][$binfo['keepprefix']][0].$ph;

            if(isset($binfo['keepsuffix']))

                $ph .= $minfo[1][$binfo['keepsuffix']][0];

            

            $code .= substr($codetemp, 0, $pos-$offset).$ph;

            $len = strlen($text);

            $codetemp = substr($codetemp, $pos+$len - $offset);

            $offset = $pos + $len;

            

            //$code .= substr($codetemp, $offset, $pos-$offset).$placeholders[0];

            //$offset = $pos + strlen($text);

            

            //$len = strlen($text);

            //$code = substr_replace($code, $placeholders[0], $pos-$offset, $len);

            //$offset += $len - strlen($placeholders[0]);

            

            unset($block_matches[$pos]); // we're done with this block

        }

    } while($redo);

    //$code .= substr($codetemp, $offset);

    $code .= $codetemp;

    //unset($codetemp);

    unset($block_matches);

    

    

    $i=-1;

    $secondary_matches = array();

    if($callback)

    {

        $callback($code, $secondary_matches);

        $i = count($secondary_matches)-1;

    }

    

    

    foreach($secondaries as $secondary)

    {

        preg_match_all($secondary['pattern'], $code, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        $codetemp = $code;

        $offset = 0;

        $code = '';

        ++$i;

        

        foreach($matches as $match)

        {

            if(isset($secondary['replacement']))

                $secondary_matches[$i][] = preg_replace($secondary['pattern'], $secondary['replacement'], $match[0][0]);

            elseif(isset($secondary['soffset']) || isset($secondary['eoffset']))

                $secondary_matches[$i][] = offset_join_htmlspecialchars($match[0][0], $secondary['prefix'], $secondary['suffix'], $secondary['soffset'], $secondary['eoffset']);

            else

                $secondary_matches[$i][] = $secondary['prefix'].htmlspecialchars($match[0][0]).$secondary['suffix'];

            

            

            $ph = $placeholders[$i+1];

            if(isset($secondary['keepprefix']))

                $ph = $match[$secondary['keepprefix']][0].$ph;

            if(isset($secondary['keepsuffix']))

                $ph .= $match[$secondary['keepsuffix']][0];

            

            

            //$code .= substr($codetemp, 0, $match[0][1]-$offset).$ph;

            //$len = strlen($match[0][0]);

            //$codetemp = substr($codetemp, $match[0][1]+$len - $offset);

            //$offset = $match[0][1] + $len;

            

            $code .= substr($codetemp, $offset, $match[0][1]-$offset).$ph;

            $offset = $match[0][1] + strlen($match[0][0]);

            

            //$len = strlen($match[0][0]);

            //$code = substr_replace($code, $ph, $match[0][1]-$offset, $len);

            //$offset += $len - strlen($placeholders[$i+1]);

            

        }

        //$code .= $codetemp;

        $code .= substr($codetemp, $offset);

    }

    //$code .= $codetemp;

    unset($codetemp);

    

    $code = htmlspecialchars($code);

    foreach($tertiaries as $tertiary)

    {

        $code = preg_replace($tertiary['pattern'], $tertiary['replacement'], $code);

    }

    

    

    foreach($keywords as $kinfo)

    {

        if(isset($kinfo['case']) && $kinfo['case']) $func = 'strpos';

        else $func = 'stripos';

        

        if(isset($kinfo['bsfunc'])) $bsfunc = $kinfo['bsfunc'];

        else $bsfunc = 'is_variablechar';

        if(isset($kinfo['befunc'])) $befunc = $kinfo['befunc'];

        else $befunc = 'is_variablechar';

        

        foreach($kinfo['keywords'] as $word)

        {

            $codetemp = $code;

            $code = '';

            while(($pos = $func($codetemp, $word)) !== false)

            {

                $endpos = strlen($word) + $pos;

                // below line is required to preserve case

                $myword = substr($codetemp, $pos, strlen($word));

                // check boundaries

                //die(var_dump(array($codetemp{$pos-1}, $codetemp{$endpos}, $pos)));

                if((!$pos || !$bsfunc($codetemp{$pos-1})) && (!isset($codetemp{$endpos}) || !$befunc($codetemp{$endpos})))

                    $code .= substr($codetemp, 0, $pos).$kinfo['prefix'].$myword.$kinfo['suffix'];

                else

                    $code .= substr($codetemp, 0, $pos).$myword;

                

                $codetemp = substr($codetemp, $endpos);

            }

            $code .= $codetemp;

        }

    }

    

    

    ++$i;

    while($i--)

    {

        if(!empty($secondary_matches[$i]))

            $code = our_str_replace($placeholders[$i+1], $secondary_matches[$i], $code);

    }

    

    // replace back quotes/comments etc

    if(!empty($replaced_blocks))

        $code = our_str_replace($placeholders[0], $replaced_blocks, $code);

    

    return $code;

}



function is_variablechar($char)

{

    $ord = ord($char);

    return (($ord >= 65 && $ord <=90) || ($ord >=97 && $ord <= 122) || ($ord >= 48 && $ord <= 57) || $ord == 95);

}





function offset_join_htmlspecialchars($text, $prefix, $suffix, $soffset, $eoffset)

{

    if($eoffset)

        return substr($text, 0, $soffset).$prefix.htmlspecialchars(substr($text, $soffset, -$eoffset)).$suffix.substr($text, -$eoffset);

    else

        return substr($text, 0, $soffset).$prefix.htmlspecialchars(substr($text, $soffset)).$suffix;

}







function init_code(&$code)

{

    // insert a newline at end of file for good measure

    $code .= "\n";

    

    // code not allowed to contain our special characters

    return ($code = strtr($code, array("\x00" => '', "\x01" => '')));

}

function end_code(&$code)

{

    // remove the newline we added earlier

    return str_replace(get_placeholder(8), 'class', ($code = substr($code, 0, -1)));

}



// generates a unique "placeholder" string

function get_placeholder($num)

{

    return "\x01".str_repeat("\x00", $num+1)."\x01";

}





// replaces a token with an array of replacements

function our_str_replace($find, &$replacements, $subject)

{

    $l = strlen($find);

    // allocate some memory

    $new_subject = str_repeat(' ', strlen($subject));

    $new_subject = '';

    foreach($replacements as $r)

    {

        if(($pos = strpos($subject, $find)) === false) break;

        $new_subject .= substr($subject, 0, $pos).$r;

        $subject = substr($subject, $pos+$l);

        // the above appears to be the fastest method

        //$subject = substr_replace($subject, $r, $pos, $l);

        //$subject = substr($subject, 0, $pos).$r.substr($subject, $pos+strlen($find));

    }

    $new_subject .= $subject;

    return $new_subject;

}



// this will try to put a <div> around the code, if newlines are present

function block_background($code, $class)

{

    return '<span class="'.$class.'">'.$code.'</span>';

    

    // oddly - browsers seem to have issues with the following, and copy+paste...

    /*

    $pre = '<span class="'.$class.'">';

    $s = strpos($code, "\n");

    if($s === false) return $pre.$code.'</span>';

    $e = strrpos($code, "\n");

    if($s == $e) return $pre.$code.'</span>';

    

    // okay, so we have two distinct newlines - do appropriate wrapping

    if($s == 0)

        $startbit = '';

    else

        $startbit = $pre.substr($code, 0, $s).'</span>';

    if($e == strlen($code)-1)

        $endbit = '';

    else

        $endbit = $pre.substr($code, $e).'</span>';

    

    return $startbit.'<div class="'.$class.'">'.substr($code, $s+1, $e-$s-1).'</div>'.$endbit;

    */

}



function debug_var($var)

{

    header('Content-type: text/plain');

    print_r($var);

    exit;

}

?>
