<?php
/**
 *  Renamenode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used mainly to rename a tree node.
 *
 *  Conventions:
 * The jstree must use GET to send NODE id and the 'new' text of the node
 * with names 'id' and 'newname'
 * See first lines of run().
 * It echoes true if the rename was done successfull else echoes false.
 *
 *  Callback:
 * <pre>
 *        'onrename'=>'js:function(NODE, TREE_OBJ, RB)  {
 *           var rnm=false;
 *           $.ajax({
 *               url: "'.$this->createUrl('renamenode').'" ,
 *               global: false,
 *               type: "GET",
 *               async: false,
 *               data: ({id : NODE.id , newname : TREE_OBJ.get_text(NODE)  }),
 *               success: function(rnmreturn){
 *                    rnm=rnmreturn;
 *               }
 *           });
 *           if ( !rnm ) {
 *               alert("Node with name "+TREE_OBJ.get_text(NODE)+" already exist.");
 *               jQuery.tree.rollback(RB);
 *           }
 *       }',
 * </pre>
 * @version 0.3beta
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Renamenode extends CAction {
    public function run() {
        $id=$_GET['id'];
        $node= CActiveRecord::model($this->getController()->classname)->findByPk($id);
        $newname=$_GET['newname'];
        $checkname = true;
        if($node->isRoot()) {
            if( $node->hasManyRoots ) {
                $siblings = CActiveRecord::model($this->getController()->classname)->roots()->findall();
            } else {
                $checkname = false;
            }
        } else {
            $parent=$node->parent();
            $siblings=$parent->children()->findAll();
        }
   
        
        $node->setAttribute($this->getController()->text,$newname);

        if( $checkname && $this->getController()->nameExist($siblings,$node)) {
            echo false;
            die;
        }
        $node->validate();
        if($node->save()) {
            echo true;
        }
    }
}
?>
