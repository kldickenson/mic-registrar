<?php

namespace Drupal\ro_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class RoSearchSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ro_search_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ro_search.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ro_search.settings');

    $form['google_cse_id'] = [
      '#required' =>  TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Google CSE ID'),
      '#default_value' => $config->get('google_cse_id'),
    ];

    $form['results_page'] = [
      '#required' =>  TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Results Page'),
      '#default_value' => $config->get('results_page'),
    ];


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $this->config('ro_search.settings')
      ->set('google_cse_id', $values['google_cse_id'])
      ->set('results_page', $values['results_page'])
      ->save();

    parent::submitForm($form, $form_state);
  }


}

