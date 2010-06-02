<?php
/**
 *  Render:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used mainly to render the tree nodes (root and childs).
 * Can be used in tries with only one root.
 *
 *  Conventions:
 * The jstree must use GET to send NODE id with name 'id' this will happen
 * when user wants to open a node with childs.
 * Ajax shoul make GET request with id =0 to render the root in 1st time
 * it's called.
 * See first lines of run().
 *
 *  Callback:
 * 'beforedata'=>'js: function(NODE, TREE_OBJ) { return { id : $(NODE).attr("id") || 0 }; }'
 *
 * @version 0.3beta
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Render extends CAction {
    public function run(){
		header('Cache-Control: max-age=0,no-cache,no-store,post-check=0,pre-check=0');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');		

        $id=$_GET['id'];

        if ( $id==0 ) {
            $nodestoformat = CActiveRecord::model($this->getController()->classname)->roots()->findall();
        } else {
            $node= CActiveRecord::model($this->getController()->classname)->findByPk($id);
            $nodestoformat=$node->children()->findall();
        }

        $rootsdata=$this->getController()->formatNode($nodestoformat);
        echo CJSON::encode($rootsdata);

    }
}