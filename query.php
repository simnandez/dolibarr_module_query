<?php

require('config.php');

dol_include_once('/query/class/query.class.php');

$langs->load('query@query');


$action = GETPOST('action');

$query=new TQuery;
$PDOdb=new TPDOdb;


switch ($action) {
	
	case 'clone':
		$query->load($PDOdb, GETPOST('id'));
		$query->rowid = 0;
		$query->title.=' ('.$langs->trans('Copy').')';
		$query->save($PDOdb);
		fiche($query);
		
		break;
	
	case 'set-expert':
		$query->load($PDOdb, GETPOST('id'));
		$query->expert = 1;
		$query->save($PDOdb);
		fiche($query);
	
		break;
	case 'unset-expert':
		$query->load($PDOdb, GETPOST('id'));
		$query->expert = 0;
		$query->save($PDOdb);
		fiche($query);
	
		break;
	case 'view':
		
		$query->load($PDOdb, GETPOST('id'));
		fiche($query);
		
		break;
	case 'add':
		
		if(empty($user->rights->query->all->create)) accessforbidden();
		
		fiche($query);
		
		break;
		
	case 'run':
		$query->load($PDOdb, GETPOST('id'));
		run($PDOdb, $query);
		
		break;		

	case 'preview':
		$query->load($PDOdb, GETPOST('id'));
		run($PDOdb, $query, true);
		
		break;

	default:
		
		liste();
		
		break;
}




function run(&$PDOdb, &$query, $preview = false) {
	global $conf;
	
	if(!$preview) {
		llxHeader('', 'Query', '', '', 0, 0, array() , array('/query/css/query.css') );
		dol_fiche_head();
	}
	else{
		
		?><html>
			<head>
				<link rel="stylesheet" type="text/css" href="<?php echo dol_buildpath('/theme/eldy/style.css.php',1) ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo dol_buildpath('/query/css/query.css',1) ?>">
				<script type="text/javascript" src="<?php echo dol_buildpath('/includes/jquery/js/jquery.min.js',1) ?>"></script>
			</head>
		<body style="margin:0 0 0 0;padding:0 0 0 0;"><?php
		
	}
	
	if(empty($query->sql_from)) die('InvalidQuery');
	
	$show_details = true;
	
	if($preview) {
		$query->preview = true;
		
	}
	
	echo $query->run($PDOdb, $show_details);
	
	if(!$preview) {
		dol_fiche_end();
		llxFooter();
		
	}
	else{
		?></body></html><?php
	}
}


function liste() {
	
	global $langs, $conf,$user;
	
	$PDOdb=new TPDOdb;
	
	llxHeader('', 'Query', '', '', 0, 0, array() , array('/query/css/query.css') );
	dol_fiche_head();
	
	$sql="SELECT rowid as 'Id', title,expert
	FROM ".MAIN_DB_PREFIX."query
	WHERE 1
	 ";
	
	$r=new TListviewTBS('lQuery');
	echo $r->render($PDOdb, $sql,array(
		'link'=>array(
			'Id'=>'<a href="?action=view&id=@val@">'.img_picto('Edit', 'edit.png').' @val@</a>'
			,'title'=>'<a href="?action=run&id=@Id@">'.img_picto('Run', 'object_cron.png').' @val@</a>'
		)
		,'title'=>array(
			'title'=>$langs->trans('Title')
			,'expert'=>$langs->trans('Expert')
		)
		,'translate'=>array(
			'expert'=>array( 0=>$langs->trans('No'), 1=>$langs->trans('Yes') )
		)
	
	));
	
	dol_fiche_end();
	
	llxFooter();
}

function init_js(&$query) {
	
	if(!empty($query->TMode)) {
		foreach($query->TMode as $f=>$v) {
			
			echo ' $("#fields [sql-act=\'mode\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
		
	}
	
	if(!empty($query->TOrder)) {
		foreach($query->TOrder as $f=>$v) {
		
			echo ' $("#fields [sql-act=\'order\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
				
	if(!empty($query->TOperator)) {
		foreach($query->TOperator as $f=>$v) {
			
			echo ' $("#fields [sql-act=\'operator\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->TValue)) {
		foreach($query->TValue as $f=>$v) {
			
			echo ' $("#fields [sql-act=\'value\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->THide)) {
		foreach($query->THide as $f=>$v) {
			
			echo ' $("#fieldsview [sql-act=\'hide\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->TTitle)) {
		foreach($query->TTitle as $f=>$v) {
			
			echo ' $("#fieldsview [sql-act=\'title\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->TTranslate)) {
		foreach($query->TTranslate as $f=>$v) {
			
			echo ' $("#fieldsview [sql-act=\'translate\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->TFunction)) {
		foreach($query->TFunction as $f=>$v) {
			
			echo ' $("#fields [sql-act=\'function\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->TGroup)) {
		foreach($query->TGroup as $f) {
			
			echo ' $("#fields [sql-act=\'group\'][field=\''.$f.'\']").val(1); ';
			
		}
	}

	if(!empty($query->TTotal)) {
		foreach($query->TTotal as $f=>$v) {
			
			echo ' $("[sql-act=\'total\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->TFilter)) {
		foreach($query->TFilter as $f=>$v) {
			
			echo ' $("[sql-act=\'filter\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); $("[sql-act=\'filter\'][field=\''.$f.'\']").show(); ';
			
		}
	}

	if(!empty($query->TType)) {
		foreach($query->TType as $f=>$v) {
			
			echo ' $("[sql-act=\'type\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
	if(!empty($query->TClass)) {
		foreach($query->TClass as $f=>$v) {
			
			echo ' $("[sql-act=\'class\'][field=\''.$f.'\'],[sql-act=\'class-select\'][field=\''.$f.'\']").val("'. addslashes($v) .'"); ';
			
		}
	}
	
}

function fiche(&$query) {
	global $langs, $conf,$user;
	
	llxHeader('', 'Query', '', '', 0, 0, array('/query/js/query.js'/*,'/query/js/jquery.base64.min.js'*/) , array('/query/css/query.css') );
	dol_fiche_head();
	
	?>
	<script type="text/javascript">
		var MODQUERY_INTERFACE = "<?php echo dol_buildpath('/query/script/interface.php',1); ?>";
		var MODQUERY_QUERYID = <?php echo $query->getId(); ?>;
		var MODQUERY_EXPERT = <?php echo (int)$query->expert; ?>;
		
		var select_equal = '<select sql-act="operator"> '
					+ '<option value=""> </option>'
					
					+ '<option value="LIKE">LIKE</option>'
					+ '<option value="=">=</option>'
					+ '<option value="!=">!=</option>'
					+ '<option value="&lt;">&lt;</option>'
					+ '<option value="&lt;=">&lt;=</option>'
					+ '<option value="&gt;">&gt;</option>'
					+ '<option value="&gt;=">&gt;=</option>'
					+ '<option value="IN">IN</option>'
					+ '</select>';
					
		var select_mode	= '<select sql-act="mode"> '
					+ '<option value="value">valeur</option>'
					+ '<option value="var">variable</option>'
					+ '<option value="function">fonction</option>'
					+ '</select> <input type="text" value="" sql-act="value" />';
			
		var select_order	= '<select sql-act="order"> '
					+ '<option value=""> </option>'
					+ '<option value="ASC">Ascendant</option>'
					+ '<option value="DESC">Descendant</option>'
					+ '</select>';
			
		var select_filter	= '<select sql-act="filter"> '
					+ '<option value="">Libre</option>'
					+ '<option value="calendar">Date</option>'
					+ '<option value="calendars">Dates</option>'
					+ '</select>';
			
		
			
		var select_hide	= '<select sql-act="hide"> '
					+ '<option value=""> </option>'
					+ '<option value="1"><?php echo $langs->trans('Hidden') ?></option>'
					+ '</select>';
			
		var select_group	= '<select sql-act="group"> '
					+ '<option value=""> </option>'
					+ '<option value="1">Groupé</option>'
					+ '</select>';
			
		var select_total	= '<select sql-act="total"> '
					+ '<option value=""> </option>'
					+ '<option value="sum">Total</option>'
					+ '<option value="average">Moyenne</option>'
					+ '<option value="count">Nombre</option>'
					+ '</select>';
			
		var select_type	= '<select sql-act="type"> '
					+ '<option value=""> </option>'
					+ '<option value="number">Nombre</option>'
					+ '<option value="datetime">Date/Heure</option>'
					+ '<option value="date">Date</option>'
					+ '<option value="hour">Heure</option>'
					+ '</select>';
					
		var select_function	= '<input type="text" size="10" sql-act="function" value="" /><select sql-act="function-select"> '
					+ '<option value=""> </option>'
					+ '<option value="SUM(@field@)">Somme</option>'
					+ '<option value="ROUND(@field@,2)">Arrondi 2 décimal</option>'
					+ '<option value="COUNT(@field@)">Nombre de</option>'
					+ '<option value="MIN(@field@)">Minimum</option>'
					+ '<option value="MAX(@field@)">Maximum</option>'
					+ '<option value="MONTH(@field@)">Mois</option>'
					+ '<option value="YEAR">Année</option>'
					+ '<option value="DATE_FORMAT(@field@, \'%m/%Y\')">Année/Mois</option>'
					//+ '<option value="FROM_UNIXTIME(@field@,\'%H:%i\')">Timestamp</option>'
					+ '<option value="SEC_TO_TIME(@field@)">Timestamp</option>'
					//+ '<option value="(@field@ / 3600)">/ 3600</option>'
					+ '</select>';
		
		var select_class = '<input type="text" size="10" sql-act="class" value="" placeholder="<?php echo $langs->trans('Classname'); ?>" /><select sql-act="class-select"> '
						+ '<option value=""> </option>'
			<?php
				foreach($query->TClassName as $class=>$label) {
					echo ' +\'<option value="'.$class.'">'.$label.'</option>\'';
				}
			?>
			+ '</select>';
		
		function _init_query() {
			
			<?php

			if($query->getId()>0) {
				
				if($query->expert) {
				
					echo 'showQueryPreview('.$query->getId().');';
						
					if(!empty($query->sql_fields)) {
						$query->TField = explode(',', $query->sql_fields );
					}
					
					if(!empty($query->TField )) {
						foreach($query->TField as $field) {
							
							echo ' refresh_field_param("'.$field.'"); ';
						
						}
					}
					init_js($query);
				}
				else {
					
					foreach($query->TTable as $table) {
						
						echo 'addTable("'.$table.'"); ';
			
					}
				
					if(empty($query->TField) && !empty($query->sql_fields)) {
						$query->TField = explode(',', $query->sql_fields );
					}
					//$TField = 
					if(!empty($query->TField )) {
						foreach($query->TField as $field) {
							
							echo ' checkField("'.$field.'"); ';
						
						}
						
						echo 'showQueryPreview('.$query->getId().');';
						
					}
					
					init_js($query);
				
					if(!empty($query->TJoin)) {
						foreach($query->TJoin as $t=>$join) {
							
							?>
							$("td[rel=from] select[jointure='<?php echo $t; ?>']").val("<?php echo $join[0]; ?>");
							$("td[rel=to] select[jointure-to='<?php echo $t; ?>']").val("<?php echo $join[1]; ?>");
							
							TJoin['<?php echo $t; ?>'] = ["<?php echo $join[0]; ?>", "<?php echo $join[1]; ?>"]; 
							<?php
							
						}
					}
							
					?>
					refresh_sql();
					<?php
					
				}
			}
						
			?>
		}
		
	</script>
	
	<form name="formQuery" id="formQuery">
		<input type="hidden" name="id" value="<?php echo $query->getId(); ?>" />
		
	<div>
		<?php
			if($query->getId()>0 && !empty($user->rights->query->all->expert) ) {
				?><div style="float:right;"><?php 
					
				if(!$query->expert) {
					
					?><a class="butAction" href="?action=set-expert&id=<?php echo $query->getId() ?>"><?php echo $langs->trans('setExpertMode') ?></a><?php
					
				}
				else {
					?><a class="butAction" href="?action=unset-expert&id=<?php echo $query->getId() ?>"><?php echo $langs->trans('unsetExpertMode') ?></a><?php
				}
				
				?><br /><br /><a class="butAction" href="?action=clone&id=<?php echo $query->getId() ?>"><?php echo $langs->trans('cloneQuery') ?></a><?php
				
				?></div><?php
				
			}
		?>
		<div>
			<?php echo $langs->trans('Title') ?> : 
			<input type="text" name="title" size="80" value="<?php echo $query->title; ?>" />
			<?php
				$form=new TFormCore;
				echo $form->combo('- '.$langs->trans('Type').' : ', 'type', $query->TGraphiqueType, $query->type);
				echo '- '.$langs->trans('XAxis').' : <select name="xaxis" initValue="'.$query->xaxis.'"></select>';
			?>
			<input class="button" type="button" id="save_query" value="<?php echo $langs->trans('SaveQuery') ?>" />
		</div>
		<?php
		if($query->getId()>0 && !$query->expert) {
		?>
		<div>
			<?php echo $langs->trans('AddOneOfThisTables') ?> : <select id="tables"></select>
			<input class="button" type="button" id="add_this_table" value="<?php echo $langs->trans('AddThisTable') ?>" />
		</div>
		
		<div id="selected_tables">
			
		</div>
		<?php
		}
		?>
	</div>
	<?php
		if($query->getId()>0) {
			?>
			<div class="selected_fields">
				<div class="border" id="fields"><div class="liste_titre"><?php echo $langs->trans('FieldsOrder'); ?></div></div>
			</div>
			<?php
		}
		
		if($query->getId()>0) {
	?>
	<div id="results" style="display:<?php echo !$query->expert ? 'none':'block'; ?>;">
		<div>
		<?php echo $langs->trans('Fields'); ?><br />
		<textarea id="sql_query_fields" name="sql_fields"><?php echo $query->sql_fields ?></textarea>
		</div>
		
		<div>
		<?php echo $langs->trans('From'); ?><br />
		<textarea id="sql_query_from" name="sql_from"><?php echo $query->sql_from ?></textarea>
		</div>
		
		<div>
		<?php echo $langs->trans('Where'); ?><br />
		<textarea id="sql_query_where" name="sql_where"><?php echo $query->sql_where ?></textarea>
		
		<?php
			if($query->expert) {
				echo $langs->trans('AfterWhere'); ?><br /><textarea id="sql_query_afterwhere" name="sql_afterwhere"><?php echo $query->sql_afterwhere ?></textarea><?php
			}
			else {
				?><input type="hidden" id="sql_query_afterwhere" name="sql_afterwhere" value="" /><?php
			}
		
		?>
		
		</div>
	</div>
	
	<div style="clear:both; border-top:1px solid #000;"></div>
	<?php
		if($query->getId()>0) {
	?>
	<div class="selected_fields_view">
		<div class="border" id="fieldsview"><div class="liste_titre"><?php echo $langs->trans('FieldsView'); ?></div></div>
	</div>
	<?php
		}
	?>
	<div id="previewRequete" style="display: none;">
		<iframe src="#" width="100%" frameborder="0" onload="this.height = this.contentWindow.document.body.scrollHeight + 'px'"></iframe>
	</div>
	
	<?php
		}
	?>
	</form>
	
	
	
	<?php
	dol_fiche_end();
	
	llxFooter();
}
	



