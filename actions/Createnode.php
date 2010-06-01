<?php
/**
 *  Createnode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to create a new node in tree.
 *  Conventions:
 * The jstree must use GET to send REF_ID's id with name 'ref_id'
 * and TYPE with name 'type'
 * See first lines of run().
 *  'oncreate cCallback for jstree:
 * <pre>
 *       'oncreate'=>'js:function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
 *               var crt=false;
 *               $.ajax({
 *                   url: "'.$this->createUrl('createnode').'" ,
 *                   global: false,
 *                   type: "GET",
 *                   async: false,
 *                   data: ({ref_id : REF_NODE.id , type : TYPE }),
 *                   dataType: "json",
 *                   success: function( jsondata ){
  *                       if ( jsondata ) {
 *                           $(NODE).attr("id",jsondata.attributes.id);
 *                           $(NODE).children("a:eq(0)").html("<ins>&nbsp;</ins>"+jsondata.data);
 *                           crt=true;
 *                       }
 *                   }
 *               });
 *           if ( !crt ) {
 *               alert("Could not create node "+TYPE+" "+TREE_OBJ.get_text(REF_NODE));
 *               jQuery.tree.rollback(RB);
 *           }
 *       }',
 * </pre>
 * 
 * @version 0.3beta
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Createnode extends CAction {
    /**
     * This method calls the insertingnode() method from EBehavior.php
     */
    public function run( ) {

        $defaultname = ( isset($this->getController()->defaultname) ) ? $this->getController()->defaultname : "New Folder" ;

        $refid=$_GET['ref_id'];
        $type=$_GET['type'];

        $refnode= CActiveRecord::model($this->getController()->classname)->findByPk($refid);

        $modelclass=$this->getController()->classname;
        $newnode=new $modelclass();
        $newnode->setAttribute($this->getController()->text,$defaultname);

        //echo $this->getController()->insertingnode( $newnode,$refnode,$type,$this->getController()->nodenaming,$this->getController()->crtinherit );

        echo $this->getController()->insertingnode( $newnode,$refnode,$type,true,$this->getController()->crtinherit);
    }

}
?>
