<?php

namespace Drupal\webform\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformInterface;

/**
 * Plugin implementation of the 'webform_entity_reference_select' widget.
 *
 * @FieldWidget(
 *   id = "webform_entity_reference_select",
 *   label = @Translation("Select list"),
 *   description = @Translation("A select menu field."),
 *   field_types = {
 *     "webform"
 *   }
 * )
 *
 * @see \Drupal\webform\Plugin\Field\FieldWidget\WebformEntityReferenceAutocompleteWidget
 * @see \Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget
 * @see \Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget
 */
class WebformEntityReferenceSelectWidget extends WebformEntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'default_data' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = [];
    $element['default_data'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable default submission data (YAML)'),
      '#description' => t('If checked, site builders will be able to define default submission data (YAML)'),
      '#default_value' => $this->getSetting('default_data'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = t('Default submission data: @default_data', ['@default_data' => $this->getSetting('default_data') ? $this->t('Yes') : $this->t('No')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Convert 'entity_autocomplete' to 'webform_entity_select' element.
    $element['target_id']['#type'] = 'webform_entity_select';

    /** @var \Drupal\webform\WebformEntityStorageInterface $webform_storage */
    $webform_storage = \Drupal::service('entity_type.manager')->getStorage('webform');
    $element['target_id']['#options'] = $webform_storage->getOptions(FALSE);

    // Set empty option.
    if (empty($element['#required'])) {
      $element['target_id']['#empty_option'] = $this->t('- Select -');
      $element['target_id']['#empty_value'] = '';
    }

    // Convert default_value's Webform to a simple entity_id.
    if (!empty($element['target_id']['#default_value']) && $element['target_id']['#default_value'] instanceof WebformInterface) {
      $element['target_id']['#default_value'] = $element['target_id']['#default_value']->id();
    }

    // Make sure if an archived webform is the #default_value always include
    // it as an option.
    if (!empty($element['target_id']['#default_value'])) {
      $webform = ($element['target_id']['#default_value'] instanceof WebformInterface) ? $element['target_id']['#default_value'] : Webform::load($element['target_id']['#default_value']);
      if ($webform && $webform->isArchived()) {
        $element['target_id']['#options'][(string) t('Archived')][$webform->id()] = $webform->label();
      }
    }

    // Remove properties that are not applicable.
    unset($element['target_id']['#size']);
    unset($element['target_id']['#maxlength']);
    unset($element['target_id']['#placeholder']);

    $element['#element_validate'] = [[get_class($this), 'validateWebformEntityReferenceSelectWidget']];

    return $element;
  }

  /**
   * Webform element validation handler for entity_select elements.
   */
  public static function validateWebformEntityReferenceSelectWidget(&$element, FormStateInterface $form_state, &$complete_form) {
    // Below prevents the below error.
    // Fatal error: Call to a member function uuid() on a non-object in
    // core/lib/Drupal/Core/Field/EntityReferenceFieldItemList.php.
    $value = (!empty($element['target_id']['#value'])) ? $element['target_id']['#value'] : NULL;
    $form_state->setValueForElement($element['target_id'], $value);
  }

}
