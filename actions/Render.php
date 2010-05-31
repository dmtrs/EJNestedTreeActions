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
 * TODO: work with multiple roots.
 * @version 0.1
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Render extends CAction {
    public function run() {
        $id=$_GET['id'];
        if ( $id==0 ) {
            $roots = CActiveRecord::model($this->getController()->classname)->roots()->findall();
            $rootsdata=$this->getController()->formatNode($roots);
            //$jsondata='[ '.CJSON::encode($rootsdata).' ]';
//            foreach($rootsdata as $i=>$root ) {
//                $jsondata.=CJSON::encode($root);
//            
//            }
            fb(CJSON::encode($rootsdata));die;
        } else {

            $node= CActiveRecord::model($this->getController()->classname)->findByPk($id);

            $childs=$node->children()->findall();

            $jsondata='[ ';
            foreach( $childs as $i=>$eachnode ) {
                $jsondata.=CJSON::encode($this->getController()->formatNode($eachnode));
                if( $i==count($childs)-1 ) {
                    $jsondata.=' ]';
                } else {
                    $jsondata.=',';
                }
            }
        }
        echo $jsondata;
    }
}
?>
