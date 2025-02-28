<?php
use CRM_Airmail_Utils as E;

class CRM_Airmail_Page_Webhook extends CRM_Core_Page {

  public function run() {
    if (!empty($settings['secretcode']) && $settings['secretcode'] != CRM_Utils_Array::value('secretcode', $_REQUEST)) {
      $this->invalidMessage();
    }

    $backend = E::getBackend();
    // Check that this is a real backend.
    if (!$backend || !in_array('CRM_Airmail_Backend', class_implements($backend))) {
      $this->invalidMessage();
    }

    // Process the input
    $events = $backend->processInput(file_get_contents('php://input'));

    // Make sure the processed input exists and is valid according to the backend.
    if (!$events || !$backend->validateMessages($events)) {
      $this->invalidMessage();
    }

    // Process the message(s) in the processed input
    $backend->processMessages($events);

    CRM_Utils_System::civiExit();
  }

  /**
   * What should happen if we want to reject the message without processing it.
   */
  protected function invalidMessage() {
    CRM_Utils_System::setHttpHeader("Status", "404 Not Found");
    CRM_Utils_System::civiExit();
  }

}
