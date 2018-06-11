<?php

namespace Drupal\quick_node_clone\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class QuickNodeCloneSettingForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['quick_node_clone.settings'];
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'quick_node_clone_setting_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
      $settings = $this->configFactory->get('quick_node_clone.settings');
      $form['text_to_prepend_to_title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Text to prepend to title'),
        '#default_value' => $settings->get('text_to_prepend_to_title'),
        '#description' => $this->t('Enter text to add to the title of a cloned node to help content editors. A space will be added between this text and the title. Example: "Clone of"')
      ];
      return parent::buildForm($form, $form_state);
  }
   /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      $text_to_prepend_to_title = $form_state->getValue('text_to_prepend_to_title');
      $this->config('quick_node_clone.settings')->set('text_to_prepend_to_title', $text_to_prepend_to_title)->save();
  }
}