<?php

namespace Sb\ZendForm;

use Sb\Entity\ChronicleType;
use Sb\Helpers\ChronicleHelper;
use Sb\Entity\ChronicleLinkType;
use Sb\Db\Service\TagSvc;
use Sb\Db\Model\Tag;
use Sb\Db\Model\Chronicle;
use Sb\ZendValidator\BookExists;

/**
 *
 * @author Didier
 */
class ChronicleForm extends \Zend_Form {

    /**
     *
     * @param mixed $options
     *
     * @return void
     *
     */
    public function __construct($imageUploadPath, $new) {

        parent::__construct();
        
        // Init form with group_id as it is necessary for setting the upload directory for the image
        $this->setForm($imageUploadPath, $new);
    }

    public function setNewChronicle($userId, $groupId) {

        $this->setDefaults(array(
                "user_id" => $userId,
                "group_id" => $groupId
        ));
    }

    public function setExistingChronicle(Chronicle $chronicle) {

        $this->setDefaults(array(
                "id" => $chronicle->getId(),
                "title" => $chronicle->getTitle(),
                "keywords" => $chronicle->getKeywords(),
                "text" => $chronicle->getText(),
                "link" => $chronicle->getLink(),
                "link_type" => $chronicle->getLink_type(),
                "user_id" => $chronicle->getGroup()
                    ->getId(),
                "image" => $chronicle->getImage(),
                "type" => $chronicle->getType_id(),
                "user_id" => $chronicle->getUser()
                    ->getId(),
                "group_id" => $chronicle->getGroup()
                    ->getId()
        ));
        
        if ($chronicle->getTag())
            $this->setDefault("tag_id", $chronicle->getTag()
                ->getId());
        if ($chronicle->getBook())
            $this->setDefault("book_id", $chronicle->getBook()
                ->getId());
    }

    private function setForm($imageUploadPath, $new) {

        $this->setAction('/member/chronicle/post')
            ->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        // Title element
        $titleElement = new \Zend_Form_Element_Text("title");
        $titleElement->setLabel('Titre de la chronique - OBLIGATOIRE')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addValidator(new \Zend_Validate_StringLength(3, 500))
            ->addErrorMessage(__("La longueur du titre doit être comprise entre 3 et 500 caractères", "s1b"))
            ->setDescription(__("Titre de la chronique sans caractères HTML, longueur comprise entre 3 et 500 caractères.", "s1b"));
        $titleElement->class = "input-text";
        
        // Keyword element
        $keywordsElement = new \Zend_Form_Element_Text("keywords");
        $keywordsElement->setLabel("Mots clés - OBLIGATOIRE")
            ->setRequired(true)
            ->addFilter("StripTags")
            ->addValidator(new \Zend_Validate_StringLength(3, 250))
            ->addErrorMessage(__("La longueur des mots clés doit être comprise entre 3 et 250 caractères", "s1b"))
            ->setDescription(__("Mots clés de la chronique séparés par une virgule, longueur comprise entre 3 et 250 caractères.", "s1b"));
        $keywordsElement->class = "input-text";
        
        // Text element
        $textElement = new \Zend_Form_Element_Textarea('text');
        $textElement->setLabel('Chronique - OBLIGATOIRE')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->setAttribs(array(
                "cols" => 50,
                "rows" => 6
        ))
            ->addValidator(new \Zend_Validate_StringLength(20, 5000))
            ->addErrorMessage(__("La longueur du texte doit être comprise entre 20 et 5000 caractères", "s1b"))
            ->setDescription(__("Texte de la chronique sans caractères HTML, longueur comprise entre 20 et 5000 caractères.", "s1b"));
        
        // Type element
        $typeElement = new \Zend_Form_Element_Select("type");
        $typeOptions = array(
                "" => __("Aucun", "s1b")
        );
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::BOOK_CHRONICLE);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::DISCOVERY);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::FREE);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::GAME);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::JUST_FOR_FUN);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::NEWS);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::NEWSLETTER);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::TOPS);
        $typeOptions = $this->pushTypeOption($typeOptions, ChronicleType::WEDNESDAY_COMIC);
        $typeElement->addMultiOptions($typeOptions)
            ->setRequired(true)
            ->setLabel(__("Sélectionner la catégorie - OBLIGATOIRE", "s1b"))
            ->addErrorMessage(__("La catégorie doit être renseignée.", "s1b"));
        
        // Link type element
        $linkTypeElement = new \Zend_Form_Element_Select("link_type");
        $linkTypeOptions = array(
                "" => __("Aucun", "s1b")
        );
        $linkTypeOptions = $this->pushLinkTypeOption($linkTypeOptions, ChronicleLinkType::COMIC_BOARD);
        $linkTypeOptions = $this->pushLinkTypeOption($linkTypeOptions, ChronicleLinkType::IMAGE);
        $linkTypeOptions = $this->pushLinkTypeOption($linkTypeOptions, ChronicleLinkType::OTHER);
        $linkTypeOptions = $this->pushLinkTypeOption($linkTypeOptions, ChronicleLinkType::PODCAST);
        $linkTypeOptions = $this->pushLinkTypeOption($linkTypeOptions, ChronicleLinkType::PRESS);
        $linkTypeOptions = $this->pushLinkTypeOption($linkTypeOptions, ChronicleLinkType::URL);
        $linkTypeOptions = $this->pushLinkTypeOption($linkTypeOptions, ChronicleLinkType::VIDEO);
        $linkTypeElement->addMultiOptions($linkTypeOptions)
            ->setLabel(__("Sélectionner le type de lien", "s1b"));
        
        // Link element
        $linkElement = new \Zend_Form_Element_Text("link");
        $linkElement->setLabel('Lien')
            ->addValidator("stringLength", false, array(
                "min" => 7,
                "max" => 255,
                "messages" => array(
                        \Zend_Validate_StringLength::INVALID => __("La longeur doit être comprise entre 7 et 255 caractères.", "s1b"),
                        \Zend_Validate_StringLength::TOO_LONG => __("La longeur doit être inférieure à 255 caractères.", "s1b"),
                        \Zend_Validate_StringLength::TOO_SHORT => __("La longeur doit être supérieure à 6 caractères.", "s1b")
                )
        ))
            ->addValidator("regEx", false, array(
                "pattern" => "{(^http://)|(^https://){1}}",
                "messages" => array(
                        \Zend_Validate_Regex::NOT_MATCH => __("Le lien de la chronique doit commencer par http:// ou bien https://", "s1b")
                )
        ));
        $linkElement->class = "input-text";
        
        // Book Id element
        $bookIdElement = new \Zend_Form_Element_Text("book_id");
        $bookIdElement->setLabel(__("Attacher un livre", "s1b"))
            ->addValidator(new BookExists())
            ->addValidator("int", false, array(
                "messages" => array(
                        \Zend_Validate_Int::NOT_INT => __("L'identifiant du livre doit être un chiffre", "s1b")
                )
        ))
            ->setDescription(__("Renseigner un identifiant de livre. Il s'agit du chiffre visible en toute fin de l'url de la page de détail d'un livre après le caractère \"-\"", "s1b"));
        $bookIdElement->class = "input-text";
        
        // Image Element
        $imageElement = new \Zend_Form_Element_File("image");
        $imageElement->setLabel(__("Attacher une image", "s1b"))
            ->setDestination($imageUploadPath)
            ->addValidator('Count', false, 1)
            ->addValidator('Size', false, array(
                "max" => 102400
        ))
            ->addValidator('Extension', false, 'jpg,png,gif')
            ->addValidator("NotExists", false, array(
                "messages" => array(
                        \Zend_Validate_File_NotExists::DOES_EXIST => __("L'image chargée est déjà présente. Merci de sélectionner une autre image ou bien de renommer votre image.", "s1b")
                )
        ))
            ->setMaxFileSize(102400)
            ->setDescription(__("Maximum 100 Ko, extensions .jpg, .png ou .gif", "s1b"));
        
        // Tag element
        $tagElement = new \Zend_Form_Element_Select("tag_id");
        $tags = TagSvc::getInstance()->getAllTags("label");
        $tagOptions = $this->getTagOptions($tags);
        $tagElement->setLabel(__("Tag - OBLIGATOIRE", "s1b"))
            ->addMultiOptions($tagOptions)
            ->setRequired(true)
            ->addErrorMessage(__("Le tag doit être renseignée.", "s1b"));
        
        // Facebook post checkbox element
        $postOnFacebookCheckboxElement = new \Zend_Form_Element_Checkbox("post_on_facebook");
        $postOnFacebookCheckboxElement->setLabel(__("Poster sur facebook", "s1b"));
        $postOnFacebookCheckboxElement->class = "facebook-post-checkbox";
        
        // userId element
        $userIdElement = new \Zend_Form_Element_Hidden("user_id");
        $this->removeAllDecorators($userIdElement);
        
        // Group id element
        $groupIdElement = new \Zend_Form_Element_Hidden("group_id");
        $this->removeAllDecorators($groupIdElement);
        
        // Id element
        $idElement = new \Zend_Form_Element_Hidden("id");
        $this->removeAllDecorators($idElement);
        
        // Submit button
        $submitButtonElement = new \Zend_Form_Element_Submit("submit");
        $submitButtonElement->setOptions(array(
                'label' => ($new ? __("Créer", "s1b") : __("Mettre à jour", "s1b"))
        ));
        $submitButtonElement->class = "button bt-blue-xl";
        
        // Add all elements to form
        $this->addElements(array(
                $typeElement,
                $titleElement,
                $keywordsElement,
                $textElement,
                $linkTypeElement,
                $linkElement,
                $bookIdElement,
                $tagElement,
                $imageElement,
                $postOnFacebookCheckboxElement,
                $userIdElement,
                $groupIdElement,
                $idElement,
                $submitButtonElement
        ));
    }

    public function getChronicleText() {

        return $this->getValue("text");
    }

    public function getChronicleTitle() {

        return $this->getValue("title");
    }

    public function getChronicleType() {

        return $this->getValue("type");
    }

    public function getChronicleKeywords() {

        return $this->getValue("keywords");
    }

    public function getChronicleLinkType() {

        return $this->getValue("link_type");
    }

    public function getChronicleLink() {

        return $this->getValue("link");
    }

    public function getChronicleBookId() {

        return $this->getValue("book_id");
    }

    public function getChronicleImage() {

        return $this->getValue("image");
    }

    public function getChronicleTagId() {

        return $this->getValue("tag_id");
    }

    public function getChronicleGroupId() {

        return $this->getValue("group_id");
    }

    public function getChronicleId() {

        return $this->getValue("id");
    }

    public function getChroniclePostOnFacebook() {

        return $this->getValue("post_on_facebook");
    }

    private function pushTypeOption($options, $typeId) {

        $options[$typeId] = ChronicleHelper::getTypeLabel($typeId);
        return $options;
    }

    private function pushLinkTypeOption($options, $linkTypeId) {

        $options[$linkTypeId] = ChronicleHelper::getLinkTypeLabel($linkTypeId);
        return $options;
    }

    /**
     * Remove all decorators to the specified zend form element
     * @param \Zend_Form_Element $element the element to remove the decorators from
     */
    private function removeAllDecorators(\Zend_Form_Element $element) {

        $element->removeDecorator("Description");
        $element->removeDecorator("HtmlTag");
        $element->removeDecorator("Label");
    }

    /**
     * Get an array of tags with key as tag id and value as tag value
     * @param array of Tag $tags
     * @return array with key as tag id and value as tag value
     */
    private function getTagOptions($tags) {

        $result = array(
                "" => __("Aucun", "s1b")
        );
        
        foreach ($tags as $tag) {
            /* @var $tag Tag */
            $result[$tag->getId()] = $tag->getLabel();
        }
        return $result;
    }

}