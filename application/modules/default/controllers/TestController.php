<?php

// use Doctrine\Common\Util\Debug;
// use Sb\Db\Model\User;
use Sb\Db\Model\Chronicle;
use Sb\Trace\Trace;
// use Sb\Db\Model\PressReview;
// use Sb\Db\Model\PressReviewsSubscriber;
// use Sb\Db\Dao\UserDao;
use Sb\Db\Dao\ChronicleDao;
// use Sb\Db\Dao\PressReviewDao;
// use Sb\Db\Dao\PressReviewsSubscriberDao;
// use Sb\Entity\GroupTypes;

class Default_TestController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('ajax', 'html')->initContext();
    }

    public function exampleAction() {

        try {
            //global $globalContext;

            /* @var $chronicle Chronicle */


        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function indexAction() {

        $chronicles = ChronicleDao::getInstance()->getLast(10);
        foreach ($chronicles as $chronicle) {
            /* @var $chronicle Chronicle */
            echo "<br/>nom du group : " . $chronicle->getGroup()->getName() . "<br/>" . "title : " . $chronicle->getTitle() . "<br/>" . "date de creation : " . $chronicle->getCreation_date()->format("d/m/y") . "<br/>";
        }

        //         /* @var $user User */ 
        //         $user = UserDao::getInstance()->get(14);
        //         $groupUsers = $user->getGroupusers();
        //         foreach ($groupUsers as $groupUser) {
        //             /* @var $groupUser GroupUser */
        //         	echo "groupuser group name <br/>";
        //             Debug::dump($groupUser->getGroup()->getName());
        //             foreach ($groupUser->getGroup()->getGroupusers() as $groupuserofgroupuser) {
        //             	echo "groupuser group groupuser user id <br/>";
        //                 /* @var $groupuserofgroupuser GroupUser */            	
        //                 Debug::dump($groupuserofgroupuser->getUser()->getId());
        //             }
        //             echo "groupuser group groupusers list <br/>";
        //             Debug::dump($groupUser->getGroup()->getGroupusers());
        //         }

        //         /* @var $chronicle Chronicle */
        //         $chronicle = ChronicleDao::getInstance()->get(1);
        //         echo "chronicle tag label <br/>";
        //         Debug::dump($chronicle->getTag()->getLabel());
        //         echo "chronicle book <br/>";
        //         Debug::dump($chronicle->getBook());

        //         /* @var $pss PressReviewsSubscriber */
        //         $pss = PressReviewsSubscriberDao::getInstance()->get(1);
        //         echo "pressreview subscriber email <br/>";
        //         Debug::dump($pss->getEmail());

        //         /* @var $pressreview PressReview */
        //         $pressreview = PressReviewDao::getInstance()->get(1);
        //         echo "pressreview book title <br/>";
        //         Debug::dump($pressreview->getBook()->getTitle());
        //         echo "pressreview media twitter user <br/>";
        //         Debug::dump($pressreview->getMedia()->getTwitter_user());
        //         echo "pressreview user email <br/>";
        //         Debug::dump($pressreview->getUser()->getEmail());

    }

    public function getId(Sb\Db\Model\Model $model) {
        return $model->getId();
    }

    public function ajaxAction() {
        // action body
    }

    public function ajax2Action() {
        // action body
    }

}

