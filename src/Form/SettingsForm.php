<?php

namespace Drupal\ice_cream\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use function PHPSTORM_META\type;

class SettingsForm extends FormBase {

    /**
     * Returns a unique string identifying the form.
     *
     * The returned ID should be a unique string that can be a valid PHP function
     * name, since it's used in hook implementation names such as
     * hook_form_FORM_ID_alter().
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        // TODO: Implement getFormId() method.
        return '';
    }

    /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // TODO: Implement buildForm() method.
        $form['max_icecreams'] = [
            '#type' => 'textfield',
            '#title' => 'Max ice creams before starting the order',
            '#default_value' => \Drupal::state()->get('max_icecreams')
        ];

        $form['max_waffles'] = [
            '#type' => 'textfield',
            '#title' => 'Max waffles',
            '#default_value' => \Drupal::state()->get('max_waffles')
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Submit')
        ];

        return $form;
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
        \Drupal::state()->set('max_icecreams', $form_state->getValue('max_icecreams'));
        \Drupal::state()->set('max_waffles', $form_state->getValue('max_waffles'));
    }
}