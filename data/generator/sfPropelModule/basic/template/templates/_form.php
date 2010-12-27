[?php use_stylesheets_for_form($form) ?]
[?php use_javascripts_for_form($form) ?]

<div class="sf_admin_form">
  [?php include_partial('<?php echo $this->getModuleName() ?>/form_warning', array('form' => $form, 'action' => $action)) ?]

  [?php echo form_tag_for($form, '@<?php echo $this->params['route_prefix'] ?>') ?]
    [?php if ($configuration->getFormUseTopActions()): ?]
      [?php include_partial('<?php echo $this->getModuleName() ?>/form_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?]
    [?php endif ?]

    [?php if (method_exists($form, "get{$action}Fieldsets")): ?]
      [?php echo $form->renderHiddenFields() ?]
      [?php foreach (call_user_func(array($form, "get{$action}Fieldsets")) as $fieldset => $field_names): ?]
        <fieldset id="sf_fieldset_[?php echo preg_replace('/[^a-z0-9_]/', '_', strtolower($fieldset)) ?]"[?php echo ($form->getWidgetSchema()->getFormFormatterName() == 'table') ? " style=\"background: none; border: none;\"":""?]>
          [?php if ('NONE' != $fieldset): ?]
          <h2>[?php echo __($fieldset, array(), '<?php echo $this->getI18nCatalogue() ?>') ?]</h2>
          [?php endif; ?]
          [?php $content = "" ?]
          [?php foreach ($field_names as $name): ?]
            [?php $content .= $form[$name]->getWidget() instanceof sfWidgetFormInputHidden ? $form[$name]->render() : $form[$name]->renderRow() ?]
          [?php endforeach ?]
          [?php echo strtr($form->getWidgetSchema()->getFormFormatter()->getDecoratorFormat(), array("%content%" => $content)) ?]
        </fieldset>
      [?php endforeach ?]
    [?php else: ?]
      [?php echo strtr($form->getWidgetSchema()->getFormFormatter()->getDecoratorFormat(), array("%content%" => (string) $form)) ?]
    [?php endif ?]

    [?php include_partial('<?php echo $this->getModuleName() ?>/form_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?]
  </form>
</div>
