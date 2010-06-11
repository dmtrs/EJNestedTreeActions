EJNestedTreeActions
==================
Version. *next*
Author: [tydeas_dr](mailto:tydeas.dr@gmail.com)
Author: [Alexander Makarov, Sam Dark](mailto:sam@rmcreative.ru)

###Requirements
* Yii 1.0 or above
* ENestedSetBehavior 0.90 (see in README/Overview for a link)
* CJstree (see in overview for a link)
* [jsTree version 0.9.9.a](http://code.google.com/p/jstree/downloads/list)

### Dirs structure and installation

Extract the release file under `protected/extensions`

* EJNestedTreeBehavior/actions/Copynode.php
* EJNestedTreeBehavior/actions/Createnode.php
* EJNestedTreeBehavior/actions/Creatroot.php
* EJNestedTreeBehavior/actions/Deletenode.php
* EJNestedTreeBehavior/actions/Movenode.php
* EJNestedTreeBehavior/actions/Renamenode.php
* EJNestedTreeBehavior/actions/Render.php
* EJNestedTreeBehavior/EBehavior.php

###Usage
After you have installed ENestedSetBehavior and CJstree you should configure
ENestedSetBehavior. Add following in your controller:

~~~
[php]
public function behaviors() {
	return array(
		'EJNestedTreeActions'=>array(
			'class'=>'ext.EJNestedTreeActions.EBehavior',
			// Your model with ENestedSetBehavior
			'classname'=>'Rutree',
			'identity'=>'id',
			// model's field used as node's title
			'text'=>'text',
			// default name for created nodes
			'defaultNodeName' => 'New Folder',
			// default name for created roots
			'defaultRootName' => 'New Root',
		),
	);
}

public function actions() {
	return array (
		'render'=>'ext.EJNestedTreeActions.actions.Render',
		'createnode'=>'ext.EJNestedTreeActions.actions.Createnode',
		'renamenode'=>'ext.EJNestedTreeActions.actions.Renamenode',
		'deletenode'=>'ext.EJNestedTreeActions.actions.Deletenode',
		'movenode'=>'ext.EJNestedTreeActions.actions.Movenode',
		'copynode'=>'ext.EJNestedTreeActions.actions.Copynode',
		'createroot'=>'ext.EJNestedTreeActions.actions.Createroot',
	);
}

public function actionIndex() {
	$this->render('myview');
}
~~~

In `myview` the only thing you need is to call a widget:

~~~
[php]
<?php $this->widget('ext.EJNestedTreeActions.EJsTreeEx')?>
~~~

###Details and explanation

####Behavior
In the above example we have set some attributes to the behavior.
These attributes are the required and _must_ exist or extension won't work

#####Required attributes
_classname_ => string. The name of the model this controller handles.
_identity_ => string. The name of model's id attribute.
_text_ => string. The name of model's attribute that will be used as the name of the nodes.
For the last 2 you can refer to the ENestedSetBehavior.

#####Inherit feature
_inherit_ => array. The attribute names from which the node child will inherit values.
For example if your with countries/locations tree is about has root

	Europe
	|
	|--UK
	|  |-London
	|  |-Manchester
	|
	|--GR
	   |-Athens
	   |-Thessaloniki
	...
	...

This model may have a _Currency_ attribute that you may want to inherit every time you creat a new node.
In this occassion databse default value won't work because UK has Pounds and GR has Euro.
But for every town under the country there is the same currency. So what you have to do in behavior is:

	....
	'inherit' => array('Currency'),
	....
Or maybe you want to inherit languages as well, no problem.

	....
	'inherit' => array('Currency','language'),
	....
All the above will only happen for 3 actions create/move and copy when the corresponding boolean attribute for each action is true.
_mvinherit_ => boolean. If inherit will happen when moving node-s from parent to parent.
_cpinherit_ => boolean. If inherit will happen when coping node-s from parent to parent.
_crtinherit_ => boolean. If inherit will happen when creating node under parent.

For example if you want the inherit to appear when you create a new node:

	....
        'inherit' => array('Currency','language'),
	'crtinherit'=>true
        ....

And play ;)

####Actions
None of the above actions are required. It's up to what you want user to do with your tree.
The available actions are under folder actions and their names are self explainable.

#####Some attention here
_Createroot_ won't create a root if the NestedSet you have is single root (hasmanyroots=>false) and this is a ENestedSetBehavior issue i think (will have to check).
To create a root node for single root trees you have to do it manual with an insert to the database with this certain values.

	id=1 , lft=1 , rgt=2 , level=1

###Issues
If you are facing issues, please refer to the forum or the issues at the github site.