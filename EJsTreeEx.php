<?php
/**
 * EJsTreeEx
 */
Yii::import('ext.jsTree.CJsTree');

class EJsTreeEx extends CJsTree {
	public $rules = array(
		'droppable' => "tree-drop",
		'multiple' => true,
		'deletable' => "all",
		'draggable' => "all"
	);

	public $buttons = array(
		'rename' => array(
			'label' => 'Rename',
			'options' => array(
				'class' => 'btn edit',
				'onclick' => '$.tree.focused().rename();',
			),
		),
		'delete' => array(
			'label' => 'Delete',
			'options' => array(
				'class' => 'btn delete',
				'onclick' => '$.tree.focused().remove();',
			),
		),
		'create' => array(
			'label' => 'Create',
			'options' => array(
				'class' => 'btn add',
				'onclick' => 'var t = $.tree.focused(); if(t.selected) t.create(); else alert("Select a node first");'
			),
		),
		'cut' => array(
			'label' => 'Cut',
			'options' => array(
				'class' => 'btn cut',
				'onclick' => '$.tree.focused().cut();',
			),
		),
		'copy' => array(
			'label' => 'Copy',
			'options' => array(
				'class' => 'btn copy',
				'onclick' => '$.tree.focused().copy();',
			),
		),
		'paste' => array(
			'label' => 'Paste',
			'options' => array(
				'class' => 'btn paste',
				'onclick' => '$.tree.focused().paste();',
			),
		),
		'create_root' => array(
			'type' => 'ajax',
			'label' => 'Create root',
			'ajaxurl' => '',
			'ajaxoptions' => array(
				'global'=>false,
				'type' => "POST",
				'async'=> false,
				'success'=>'js:function(bool) {
					if (bool) {
						$.tree.focused().refresh();
					} else {
						alert("You can not create root");
					}
				}'
			),		
			'options' => array(
				'class' => 'btn add',				
			),			
		),
	);

	function init(){
		$this->buttons['create_root']['ajaxurl'] = $this->getController()->createUrl('createroot');

		$this->data = array(
        	'type' => 'json',
        	'async'=> true,
        	'opts' => array(
            	'method'=>'GET',
            	'async'=>true,
				'cache'=>false,
            	'url' => $this->getController()->createUrl('render'),
        	),
		);    	

		$this->callback = array(
        	'beforedata'=>'js:function(NODE, TREE_OBJ) { return { id : $(NODE).attr("id") || 0 }; }', // 0 means its the first time to render the tree
        	'onmove'=>'js:function(NODE, REF_NODE, TYPE, TREE_OBJ, RB){
            	var mv=false;
            	$.ajax({
                	url: "'.$this->getController()->createUrl('movenode').'" ,
                	global: false,
                	type: "POST",
                	async: false,
					cache: false,
                	data: ({id : NODE.id , ref_id : REF_NODE.id , type: TYPE }),
                	success: function( name ){
						if(name){
                            $(NODE).children("a:eq(0)").html("<ins>&nbsp;</ins>"+name);
                            mv=true;
                        }
	                }
    	        });
        	    if ( !mv ) { jQuery.tree.rollback(RB); };
        	}',
        	'onrename'=>'js:function(NODE, TREE_OBJ, RB){
            	var rnm=false;
            	$.ajax({
	                url: "'.$this->getController()->createUrl('renamenode').'" ,
    	            global: false,
        	        type: "POST",
            	    async: false,
                	cache: false,
                	data: ({id : NODE.id , newname : TREE_OBJ.get_text(NODE)  }),
                	success: function(rnmreturn){
                    	rnm=rnmreturn;
                	}
            	});
            	if ( !rnm ) {
					alert("Node with name "+TREE_OBJ.get_text(NODE)+" already exist.");
					jQuery.tree.rollback(RB);
				}
			}',
			'ondelete'=>'js:function(NODE, TREE_OBJ,RB) {
				var dl=false;
				var prompt=window.confirm("Delete "+TREE_OBJ.get_text(NODE)+" ?");
				if( prompt ) {
					$.ajax({
						url: "'.$this->getController()->createUrl('deletenode').'" ,
						global: false,
						type: "POST",
						async: false,
						cache: false,
						data: ({id : NODE.id }),
						success: function( dlsuccess ){
							dl=dlsuccess;
						}
					});
				}
				if ( dl ) {
					alert(TREE_OBJ.get_text(NODE)+" deleted.");
				} else {
					jQuery.tree.rollback(RB);
					alert(TREE_OBJ.get_text(NODE)+" has not be deleted.");

				}

			}',
			'oncreate'=>'js:function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
					var crt=false;
					$.ajax({
						url: "'.$this->getController()->createUrl('createnode').'" ,
						global: false,
						type: "POST",
						async: false,
						cache: false,
						data: ({ref_id : REF_NODE.id , type : TYPE }),
						dataType: "json",
						success: function( jsondata ){
							console.log(jsondata);
							if ( jsondata ) {
								$(NODE).attr("id",jsondata.attributes.id);
								$(NODE).children("a:eq(0)").html("<ins>&nbsp;</ins>"+jsondata.data);
								crt=true;
							}
						}
					});
				if(!crt){
					alert("Could not create node "+TYPE+" "+TREE_OBJ.get_text(REF_NODE));
					jQuery.tree.rollback(RB);
				}
			}',
			'oncopy'=>'js:function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
				var cp=false;
				$.ajax({
					url: "'.$this->getController()->createUrl('copynode').'" ,
					global: false,
					type: "POST",
					async: false,
					cache: false,
					data: ({id : NODE.id , ref_id : REF_NODE.id , type: TYPE }),
					dataType: "json",
					success: function( ){
							TREE_OBJ.refresh(NODE);
						}
				});
			}',

		"error"=>"js:function() { }",
		"ondblclk"=>"js:function(){alert('Doubleclick');TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE); TREE_OBJ.select_branch.call(TREE_OBJ, NODE); }",
		"onrgtclk"=>"js:function(NODE, TREE_OBJ, EV){
			EV.preventDefault();
			EV.stopPropagation();
			return false;
		}",
		"ondrop"=>"js:function() { alert('Foreign node dropped'); }"
		);
		parent::init();
	}

	function run(){
		foreach($this->buttons as $button){
			if(!empty($button['type']) && $button['type']=='ajax'){
				echo CHtml::ajaxButton($button['label'], $button['ajaxurl'], $button['ajaxoptions'], $button['options'])." ";		
			}
			else {
				echo CHtml::button($button['label'], $button['options'])." ";	
			}
		}
		
		parent::run();
	}
}