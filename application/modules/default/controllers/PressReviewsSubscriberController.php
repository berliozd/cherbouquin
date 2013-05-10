<?php
use Sb\ZendForm\PressReviewsSusbcriptionForm;
use Sb\Flash\Flash;
use Sb\Helpers\HTTPHelper;
use Sb\Helpers\ArrayHelper;
use Sb\Db\Model\PressReviewsSubscriber;
use Sb\Db\Dao\PressReviewsSubscriberDao;
use Sb\Trace\Trace;

/**
 *
 * @author Didier
 */
class Default_PressReviewsSubscriberController extends \Zend_Controller_Action {

    /**
     * Action called when posting press reviews subscription form
     */
    public function postAction() {

        try {
            // Form is not posted correctly, we redirect to the previous page
            if (!$this->getRequest()
                ->isPost()) {
                Flash::addItem(__("Requête invalide.", "s1b"));
                return HTTPHelper::redirectToReferer();
            }
            
            // Check the form validity
            $form = new PressReviewsSusbcriptionForm();
            if (!$form->isValid($_POST)) {
                
                // Walk through all errors to set the error flash messages
                foreach ($form->getErrors() as $errorKey => $errorValue) {
                    if ($errorValue && count($errorValue) > 0) {
                        foreach ($errorValue as $key => $value) {
                            $fieldMessages = ArrayHelper::getSafeFromArray($form->getMessages(), $errorKey, null);
                            if ($fieldMessages) {
                                $errorMessage = ArrayHelper::getSafeFromArray($fieldMessages, $value, null);
                                Flash::addItem($errorMessage);
                            }
                        }
                    }
                }
            } else {
                
                // Try to get an existing PressReviewsSubscriber
                /* @var $existingPressReviewSubscriber PressReviewsSubscriber */
                $existingPressReviewSubscriber = PressReviewsSubscriberDao::getInstance()->getByEmail($form->getEmail());
                if ($existingPressReviewSubscriber) {
                    
                    // Update press reviews subscriber (reactivation)
                    $existingPressReviewSubscriber->setIs_deleted(false);
                    PressReviewsSubscriberDao::getInstance()->update($existingPressReviewSubscriber);
                    
                    // Set success flash message
                    Flash::addItem(__("Votre abonnement a été réactivé.", "s1b"));
                } else {
                    // Add press reviews subscriber to database
                    $pressReviewsSubscribers = new PressReviewsSubscriber();
                    $pressReviewsSubscribers->setEmail($form->getEmail());
                    PressReviewsSubscriberDao::getInstance()->add($pressReviewsSubscribers);
                    
                    // Set success flash message
                    Flash::addItem(__("Votre abonnement a bien été pris en compte.", "s1b"));
                }
            }
            
            HTTPHelper::redirectToReferer();
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    /**
     * Action called for unsubscription to press reviews
     */
    public function unsubscribeAction() {

        try {
            $email = $this->getParam("email", null);
            
            if (!$email)
                
                Flash::addItem(__("Requête invalide", "s1b"));
            else {
                $email = trim($email);
                /* @var $pressReviewsSubscriber PressReviewsSubscriber */
                $pressReviewsSubscriber = PressReviewsSubscriberDao::getInstance()->getByEmail($email);
                if ($pressReviewsSubscriber) {
                    
                    // Mark the press review subscriber as deleted
                    $pressReviewsSubscriber->setIs_deleted(true);
                    PressReviewsSubscriberDao::getInstance()->update($pressReviewsSubscriber);
                    
                    Flash::addItem(__("Votre désinscription a bien été pris en compte.", "s1b"));
                } else
                    Flash::addItem(__("Il n'y a pas d'abonné correspondant à l'email fourni.", "s1b"));
            }
            
            HTTPHelper::redirectToHome();
        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

}
