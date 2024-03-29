# pmPropelGeneratorPlugin

The `pmPropelGeneratorPlugin` decouples the admin generator and the forms
framework by delegating the module behavior to the form. Also, adds to the
admin module the show action.

## Installation

* Via composer:

```json
{
  "require": {
    "desarrollo-cespi/pm-propel-generator-plugin": "dev-master"
  }
}
```

* Or using git, from source.

* Clear the cache
  
* From now on you can generate-admin with the basic theme:
  
```bash
$ php symfony propel:generate-admin <app> ClassName --theme=basic
```

* The plugin needs jQuery & the jQuery.noConflict(). These two are provided within the 'web/js' path of the plugin. First load jQuery, then the 'no-conflict' script and after this two, if needed, load prototype.

## Configuration

* Set the pmPropelGenerator class on generator.yml and the basic theme

```yml
generator:
  class: pmPropelGenerator
  param:
    # ...
    theme: basic
    # ...
```

* Configure the module as you configure the standard admin generator except
  that fields configuration and display are no longer available on new and edit
  contexts (just in list, for multiple sorting configuration).

## Features

* Per action fieldset support by adding the `get{$action}Fieldsets` method in the form:

```php
class Person extends sfFormPropel
{
  // ...

  public function getNewFieldsets()
  {
    return array(
      'NONE' => array('first_name', 'last_name'),
      'Address' => array('street', 'number', 'city_id')
    );
  }

  public function getEditFieldsets()
  {
    return $this->getNewFieldsets();
  }
}
```

Only for New and Edit actions (show action is like a list view).

* Per action layout support by adding the `get{$action}Layout` method in the form:

```php
class Person extends sfFormPropel
{
  // ...

  public function getNewLayout()
  {
    return 'tabbed'; // or 'folded'
  }

  public function getEditLayout()
  {
    return 'folded'; // or 'tabbed'
  }
}

Also you can create another layouts, creating a partial. I.E., tabbed layout is coded in _form_tabbed.php and _show_form_tabbed.php.

* Fields and display configuration through the form for new, edit and show contexts.
* The forms are displayed in tables rather than divs (as originally in forms) and thus the partials are less than in the standard generator.
* Form formatters are used if defined.
* Object actions can be forbidden if a method is defined in the object. IE:

```php
class Person extends BasePerson
{
  /**
   * this method will be used to forbid the edit action in the generator
   */
  public function canEdit($sf_user)
  {
    return !$this->getLock();
  }
}
```

The method takes one argument: the user.

new action can be forbidden defining the canNew method on Peer class. IE:

```php
class PersonPeer extends BasePersonPeer
{
  /**
   * this method will be used to forbid the new action in the generator
   */
  public function canNew($sf_user)
  {
    // do something...
    return true;
  }
}
```

* Object actions can be show on a condition. IE:

```yml
list:
  object_actions:
    _edit:
      show_when: canEdit
    # ...
```

* External column sorting. Suppose that the model Person has a foreign key:
  document_type_id, so we want to sort the list columns by document_type.
  In this case need to do this:

```yml
list:
  fields:
    document_type:
      peer_column: DocumentTypePeer::NAME
  peer_method: doSelectJoinDocumentType
  display: [first_name, last_name, document_type, document_number]
```

* Multiple sorting

```yml
list:
  multiple_sort: true
```

* New flash messages
  * 'error':                 same as before.
  * 'error_params':          this should be an array that will be used as a second parameter of the __() method. Use this for translation.
  * 'error_detail':          an array of messages to be displayed below the 'error' message.
  * 'error_detail_params':   like 'error_params' but for the 'error_detail' message.
  * 'notice':                same old story.
  * 'notice_params':         this should be an array that will be used as a second parameter of the __() method.
  * 'notice_detail'          an array of messages to be displayed below the 'notice' message. Use this for translation.
  * 'notice_detail_params':  like 'notice_params' but for the 'notice_detail' message.
  * 'warning':               same old story.
  * 'warning_params':        this should be an array that will be used as a second parameter of the __() method. Use this for translation.
  * 'warning_detail':        an array of messages to be displayed below the 'warning' message. Use this for translation.
  * 'warning_detail_params': like 'warning_params' but for the 'warning_detail' message.

* Top actions

If you want to display the form or list actions above the form or the list, you can set the 'use_top_actions' to true in the 'list' and 'form' section of the generator.yml

```yml
list:
  use_top_actions: true
form:
  use_top_actions: true
```

* Top pagination

If you want to display the paginator above the list set the 'use_top_pagination' to true in the 'list' section.

```yml
list:
  use_top_pagination: true
```

* New partials
  * In each <tr> element of the list, there's a partial that can be used to add clases to each row. The outputted code is something like:

```php
<tr class="sf_admin_row <?php include_partial('sf_admin_row_extra_classes', array('object' => $object))">
  ...
</tr>
```

* Show context as list context: display and layout are available. In this case, layout acts as the layout in the new and edit context. IE, with tabbed layout, the generator will render _show_form_tabbed.php partial.

Use it like this:

```yml
show:
  title: Showing something
  layout: tabbed
  display:
    First tab: [first_name, last_name]
    Second tab: [_full_id]
```

As you can see, you can use partials.

* A new form formatter, which adds the "required" class to required widgets' label. Usage:

```php
public function configure()
{
  $pm_formatter_table = new pmWidgetFormSchemaFormatterTable($this);
  $this->getWidgetSchema()->addFormFormatter("pm_table", $pm_formatter_table);
  $this->getWidgetSchema()->setFormFormatterName("pm_table");
}
```

* Export to CSV or EXCEL (requires sfPhpExcelPlugin to work). See the `EXPORTATION_DOCUMENTATION` file for complete documentation.

* customEdit:

If you're trying to modify an object with a customized form (which is not the same that is used in the 'edit' action) this plugin
      provides a customEdit template that use the following variables:

  * dbObject : The propel object that is beign updated.
  * form     : The form to edit the dbObject.
  * custom_form_action : The route or 'module/action' to send the form information.
  * custom_title       : The title of the page.


     The 'simpleProcessForm' and 'getObjectFromRequest' methods in the moduleAction class may came in handy when using this.

     Here's a little example of how to use this:

```php
public function executeEditInternalAreas(sfWebRequest $request)
{
  $this->Document             = $this->getRoute()->getObject();
  $this->form                 = $this->configuration->getEditInternalAreasForm($this->Document);
  $this->dbObject             = $this->form->getObject();
  $this->custom_form_action   = 'document/updateInternalAreas';
  $this->custom_title         = 'Edit internal areas of the document';

  $this->setTemplate('customEdit');
}

public function executeUpdateInternalAreas(sfWebRequest $request)
{
  $this->form                 = $this->configuration->getEditInternalAreasForm($this->getObjectFromRequest('document'));
  $this->dbObject             = $this->form->getObject();
  $this->custom_form_action   = 'document/updateInternalAreas';
  $this->custom_title         = 'Edit internal areas of the document';
  $this->simpleProcessForm($request, $this->form, '@document');

  $this->setTemplate('customEdit');
}
```

  * List title:
    * If a variable named 'title' is set when evaluating the 'indexSuccess' php code, then it's contents are used as a title.
    * If a variable named 'title_parameters' is set when evaluating the 'indexSuccess' php code, then it's contents are used as the parameters for I18N in the 'title'.
