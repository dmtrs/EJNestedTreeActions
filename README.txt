EJNestedTreeActions
==================
Version. 0.3beta
Author: [tydeas_dr](mailto:tydeas.dr@gmail.com)
Copyright &copy; 2010 [Evresis](http://www.evresis.com)


What EJNestedTreeActions IS and What IS NOT
-------------------------------------------

EJNestedTreeActions IS NOT:

* a nested set behavior for your model
* a jstree extension 

EJNestedTreeActions IS only a set of actions and a behavior and you as well need:

* [ENestedSetBehavior extension](http://code.google.com/p/yiiext/source/browse/#svn/trunk/app/extensions/yiiext/behaviors/model/trees)
* [CJstree 1.1 extension](http://www.yiiframework.com/extension/jstree/) *Attention* Cjstree must be at version 1.1 or else won't work[](http://pastebin.com/download.php?i=TH7u2Jqa)
* And of course [jsTree v.0.9.9.a](http://code.google.com/p/jstree/downloads/list)


Confused ? MVC
---------------


### Model
To make your model act like a nested set you will need the 
ENestedSetBehavior, as we said before. This extension make your model to be able
to append,prepend,return the children etc.

### View 
At view we have the pretty one jstree. To use the jstree you will need the 
CJstree extension ( found as jstree extension ). 
This will give you the ability to drag & drop, copy & paste, create new nodes etc. at your view.

### Controller
This is where EJNestedTreeActions comes. 
The jstree has the ability to use async ajax to make request and get results. This async request
will call a controller's action to do so. This is what the EJNestedTreeActions does. 
Take the request from the jstree make the appropriate append,prepend, etc for your model and 
return data to the jstree.


How all this work and the callbacks
-----------------------------------

You first create you table and use the ENestedBehavior like it is described in it's documentation.
Use the EJNestedTreeBehavior as described in documentation.
Use CJstree as described in it's documentation. *Attention:* You will need to add some proper callbacks
when you will use the jstree for each action you want to use from the EJNestedBehavior. This proper 
callbacks can be found in each actions documentation but will give you an example too.


git repository
--------------
To clone project from the github repository:

	git clone git://github.com/dmtrs/EJNestedTreeActions.git

Thanks
------

I would like to thank the CJstree author shocky, the ENestedBehavior author creocoder, samdark, the Evresis team,
the people from #yii at freenode in general and specially Javache for the help on this.


###Resources
* [Yii extension site](http://www.yiiframework.com/extension/ejnestedtreeactions/) The yii extension site.
* [Github repository](http://github.com/dmtrs/EJNestedTreeActions/) For the latest version and official issue reporting.
* [Join discussion](http://www.yiiframework.com/forum/index.php?/topic/9434-extension-ejnestedtreeactions/)
All suggestions, contributes, ideas are welcome.

