<?php

namespace Drupal\ro_ediploma\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'EDiplomaBlock' block.
 *
 * @Block(
 *  id = "ediploma_block",
 *  admin_label = @Translation("Ediploma block"),
 * )
 */
class EDiplomaBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'private_key' => '',
      'datasite_code' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['private_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Private key'),
      '#description' => $this->t('THIS IS YOUR PRIVATE API KEY - KEEP THIS KEY SESURE!'),
      '#default_value' => $this->configuration['private_key'],
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['datasite_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('DataSite code'),
      '#default_value' => $this->configuration['datasite_code'],
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '0',
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['private_key'] = $form_state->getValue('private_key');
    $this->configuration['datasite_code'] = $form_state->getValue('datasite_code');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $privateKey = $this->configuration['private_key'];
    $dsc = $this->configuration['datasite_code'];

    header("Cache-Control: no-cache, must-revalidate, max-age=0");

    header("Pragma: no-cache");

    $version = "<!–– MSutterDV_Version_1.16 ––>";

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      if (isset($_GET["dvid"])) {
        $dvid = htmlspecialchars($_GET["dvid"]);
      }
      else
      {
        $dvid = 'default';
      }
    }

    $msutter_css =  '';
    $msutter_html = '';

    //prepare data to send to MSUTTER
    $fields = array(
      'fn' => 'dvr',
      'dsc' => $dsc,
      'dvid' => $dvid,
      'dvhash' => hash_hmac('sha256', $dvid.$dsc, $privateKey),
      'postaction' => $_SERVER["PHP_SELF"]
    );

    $b=date("U",time());
    $timestamp = date(" D g:i:s A, M jS Y",$b);

    $ch= curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.michaelsutter.com/msutterapi/');
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $response = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200)
    {
      curl_close($ch);


      $msutter_html = '<div align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif;color:red">Degree Verification System is Unavailable<br><br>Please try again shortly<br><br>(communication error: '.$timestamp.')</div>'.$version;
    }
    else
    {
      curl_close($ch);
      $dvObj = json_decode($response);

      if (json_last_error() === JSON_ERROR_NONE) {
        // JSON is valid
        $msutter_css = base64_decode($dvObj->dvcss);

        //validate response by hashing the html with your private key, then compare to the dvhtmlhash value
        $hashcheck = hash_hmac('sha256', base64_decode($dvObj->dvhtml), $privateKey);
        if ($hashcheck != $dvObj->dvhtmlhash) {

          $msutter_html ='<div align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif;color:red">Authentication Error</div>'.$version;

        } else {

          if ($dvObj->dverror != '') {
            $msutter_html = base64_decode($dvObj->dvhtml);
          } else {
            $msutter_html = str_replace("{val:DiplomaImage}", $dvObj->dvimg, base64_decode($dvObj->dvhtml)).$version;
          }

        }

      } else {
        $msutter_html = '<div align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif;color:red">Degree Verification System is Unavailable<br><br>Please try again shortly<br><br>(parse error: '.$timestamp.')</div>'.$version;
      }
    }

    return [
      '#type' => 'inline_template',
      '#cache' => [
        'max-age' => 0,
      ],
      '#template' => '<style>' . $msutter_css . '</style>' . $msutter_html,
    ];
  }

}
