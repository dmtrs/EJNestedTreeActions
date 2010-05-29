<?php
/**
 * TODO: copy sub-tree as well
 *
 *  Copynode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to copy a new node in tree.
 * This action uses insertingnode method which is set in the EBehavior.php
 *   Conventions:
 * The jstree must use GET to send ID of node to be copied with name 'id',
 * REF_ID's id with name 'ref_id'and TYPE with name 'type'.
 * You may notice that the ID is exploded in first line of metho run().
 * This happens because the proper callback 'oncopy' of the jstree returns
 * an 'id' like "3_copy" or "4_copy".."ID_copy".
 *
 * 'oncopy' callback for jstree:
 * <pre>
 *      'oncopy'=>'js:function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
 *           var cp=false;
 *           $.ajax({
 *               url: "'.$this->createUrl('copynode').'" ,
 *               global: false,
 *               type: "GET",
 *               async: false,
 *               data: ({id : NODE.id , ref_id : REF_NODE.id , type: TYPE }),
 *               dataType: "json",
 *               success: function( jsondata ){
 *                   if ( jsondata ) {
 *                       $(NODE).attr("id",jsondata.attributes.id);
 *                       $(NODE).children("a:eq(0)").html("<ins>&nbsp;</ins>"+jsondata.data);
 *                       cp=true;
 *                   }
 *               }
 *           });
 *           if( !cp ) {
 *               alert("Could not copy node "+TREE_OBJ.get_text(NODE)+" "+TYPE+" "+TREE_OBJ.get_text(REF_NODE));
 *               jQuery.tree.rollback(RB);
 *           }
 *        }',
 * </pre>
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Copynode extends CAction {
    /**
     * This method calls the insertingnode() method from EBehavior.php
     */
    public function run() {
        $id=explode("_",$_GET['id']);
        $ref=$_GET['ref_id'];
        $type=$_GET['type'];

        $node=CActiveRecord::model($this->getController()->classname)->findByPk($id[0]);
        $refnode=CActiveRecord::model($this->getController()->classname)->findByPk($ref);

        $classname=$this->getController()->classname;
        $idattribute=$this->getController()->id;

        $copy=new $classname();
        $copy->attributes=$node->attributes;
        
        echo $this->getController()->insertingnode( $copy,$refnode,$type );
        //echo $this->getcontroller()->insertingnode($copy,$refnode,$type);
    }
}
?>
