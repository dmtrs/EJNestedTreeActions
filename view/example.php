<?php 
$this->Widget('application.extensions.jsTree.CjsTree', array(
    'id'=>'firstTree',
    
    'data' => array(
        'type' => 'json',
        'async'=> true,
        'opts' => array(
            'method'=>'GET',
            'async'=>true,
            'url' => $this->createUrl('render'),
           
        ),

    ),
    'ui'=>array('theme_name'=>'default'),
    'rules'=>array(
        'droppable' => "tree-drop",
        'multiple' => true,
        'deletable' => "all",
        'draggable' => "all"
        
    ),
    'plugins'=>array(
      'contextmenu'=>array(),
    ),
    'callback'=>array(
        'beforedata'=>'js: function(NODE, TREE_OBJ) { return { id : $(NODE).attr("id") || 0 }; }', // 0 means its the first time to render the tree
        'onmove'=>'js:function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
            var mv=false;
            $.ajax({
                url: "'.$this->createUrl('movenode').'" ,
                global: false,
                type: "GET",
                async: false,
                data: ({id : NODE.id , ref_id : REF_NODE.id , type: TYPE }),
                success: function( name ){
                        if ( name ) {
                            $(NODE).children("a:eq(0)").html("<ins>&nbsp;</ins>"+name);
                            mv=true;
                        }
                }
            });
            if ( !mv ) { jQuery.tree.rollback(RB); };
        }',
        'onrename'=>'js:function(NODE, TREE_OBJ, RB)  {
            var rnm=false;
            $.ajax({
                url: "'.$this->createUrl('renamenode').'" ,
                global: false,
                type: "GET",
                async: false,
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
                    url: "'.$this->createUrl('deletenode').'" ,
                    global: false,
                    type: "GET",
                    async: false,
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
                    url: "'.$this->createUrl('createnode').'" ,
                    global: false,
                    type: "GET",
                    async: false,
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
            if ( !crt ) {
                alert("Could not create node "+TYPE+" "+TREE_OBJ.get_text(REF_NODE));
                jQuery.tree.rollback(RB);
            }
        }',
        'oncopy'=>'js:function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
            var cp=false;
            $.ajax({
                url: "'.$this->createUrl('copynode').'" ,
                global: false,
                type: "GET",
                async: false,
                data: ({id : NODE.id , ref_id : REF_NODE.id , type: TYPE }),
                dataType: "json",
                success: function( ){
                        TREE_OBJ.refresh(NODE);
                    }
            });
        }',

    "error"=>"js:function() { }",
    "ondblclk"=>"js:function() { alert('Doubleclick'); TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE); TREE_OBJ.select_branch.call(TREE_OBJ, NODE); }",
    "onrgtclk"=>"js:function(NODE, TREE_OBJ, EV) {
	EV.preventDefault();
	EV.stopPropagation();
	return false;
    }",
    "ondrop"=>"js:function() { alert('Foreign node dropped'); }",
  ),


));

echo '<input onclick="$.tree.focused().rename();" value="Rename" type="button">';
echo '<input onclick="$.tree.focused().remove();" value="Delete" type="button">';
echo '<input onclick=\'var t = $.tree.focused(); if(t.selected) t.create(); else alert("Select a node first");\' value="Simple Create" type="button">';
echo '<input onclick="$.tree.focused().cut();" value="Cut" type="button">';
echo '<input onclick="$.tree.focused().copy();" value="Copy" type="button">';
echo '<input onclick="$.tree.focused().paste();" value="Paste" type="button">';
echo CHtml::ajaxButton("Create root",$this->createurl('createroot'), array ('global'=>false,
                    'type' => "GET",
                    'async'=> false,'success'=>'js:function(bool) {
                        if (bool) {
                            $.tree.focused().refresh();
                        } else {
                            alert("You can not create root");
                        }
            }' ), array ( 'id'=>'createroot'));

?>
