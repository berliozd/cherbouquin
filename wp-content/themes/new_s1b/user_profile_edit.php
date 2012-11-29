<?php
require_once 'includes/init.php';

require_once 'user_profile_edit_1.php';

get_header();
/**
 * Template Name: user_profile_edit
 */
function profilVisibilityPicto($userSettings){    
    if ($userSettings->getDisplayProfile() == \Sb\Entity\UserDataVisibility::FRIENDS) {
        echo '<img src="';
        bloginfo('stylesheet_directory') ;
        echo "/s1b_images/Cadenas_ouvert.jpg" . '" />';
        echo __("mes amis", "s1b");
    } else {
        echo '<img src="';
        bloginfo('stylesheet_directory');
        echo "/s1b_images/Cadenas_ouvert.jpg" . '" />';
        echo __("public", "s1b");
    }
}
function profilDisplayEmailPicto($userSettings) {
    if ($userSettings->getDisplayBirthDay() == \Sb\Entity\UserDataVisibility::NO_ONE) {
        echo '<img src="';
        bloginfo('stylesheet_directory');
        echo "/s1b_images/cadenas.jpg". '"/>';
    } else if ($userSettings->getDisplayBirthDay() == \Sb\Entity\UserDataVisibility::FRIENDS) {
        echo '<img src="';
        bloginfo('stylesheet_directory');
        echo "/s1b_images/cadenas_ouvert.jpg". '"/>';
        echo __("mes amis", "s1b");
    } else {
        echo '<img src="';
        bloginfo('stylesheet_directory');
        echo "/s1b_images/cadenas_ouvert.jpg". '"/>';
        echo __("public", "s1b");    
    }
}
?>

<div class="user-profile-bkg">
    <div id="content-center">
        <?php
        $profileView =new \Sb\View\UserProfile($user, $userSettings, false, false, false);
        echo $profileView->get();
        ?>
        <div class="horizontal-sep-1"></div>
        <div class="profile-edit-form">
            <form action="" method="post">                            
                <table>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Nom", "s1b"); ?>
                        </td>
                        <td>
                            <input type="text" class="input-item textinput" name="LastName_modif" id="LastName_modif" value="<?php echo $user->getLastName(); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Prénom", "s1b"); ?>
                        </td>
                        <td>
                            <input type="text" class="input-item textinput" name="FirstName_modif" id="FirstName_modif" value="<?php echo $user->getFirstName(); ?>" />
                            <?php //profilVisibilityPicto($userSettings);?>
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Identifiant", "s1b"); ?>
                        </td>
                        <td>
                            <input type="text" class="input-item textinput" name="UserName_modif" id="UserName_modif" value="<?php echo $user->getUserName(); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Anniversaire (jj/mm/aaaa)", "s1b"); ?>
                        </td>
                        <td>
                            <input type="text" class="input-item textinput birthDateField" name="BirthDay_pre_modif" id="BirthDay_pre_modif" value="<?php
                            if (!$user->getBirthDay()) {
                                echo 'dd/mm/yyyy';
                            } else {
                                echo $user->getBirthDay()->format('d/m/Y');
                            }
                            ?>" />
                            <?php //profilDisplayEmailPicto($userSettings);?>
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Adresse", "s1b"); ?>
                        </td>
                        <td>
                            <input type="text" class="input-item textinput" name="Address_modif" id="Address_modif" value="<?php echo $user->getAddress(); ?>" />
                            <?php //profilVisibilityPicto($userSettings);?>
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Ville", "s1b"); ?>
                        </td>
                        <td>
                            <input type="text" class="input-item textinput" name="City_modif" id="City_modif" value="<?php echo $user->getCity(); ?>" />
                            <?php //profilVisibilityPicto($userSettings);?>
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Code postal", "s1b"); ?>
                        </td>
                        <td>
                            <input type="text" class="input-item textinput" name="ZipCode_modif" id="ZipCode_modif" value="<?php echo ($user->getZipCode() == "0" ? "" : $user->getZipCode()); ?>" />
                            <?php //profilVisibilityPicto($userSettings);?>
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Pays", "s1b"); ?>
                        </td>
                        <td>
                            <?php
                            $countries = \Sb\Db\Dao\CountryDao::getInstance()->getAll();
                            if ($user->getCountry())                                
                                $selectedCountry = \Sb\Db\Dao\CountryDao::getInstance()->getCountryByCode($user->getCountry());
                            ?>
                            <select class="input-item selectinput" name="Country_modif">
                                <option></option>
                                <?php foreach ($countries as $country) { ?>
                                <option <?php echo (($selectedCountry && ($selectedCountry->getIso3166() == $country->getIso3166())) ? "selected" : "");?> value="<?php echo $country->getIso3166(); ?>"><?php echo ($_SESSION['WPLANG'] <> "en_US" ? $country->getLabel_french(): $country->getLabel_english()); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Langue", "s1b"); ?>
                        </td>
                        <td>
                            <?php
                            $userLang = $user->getLanguage();
                            if ($userLang == "") {
                                $userLang = __("anglais", "s1b");
                                if ($_SESSION['WPLANG'] <> "en_US") 
                                    $userLang = __("français", "s1b");                                                                    
                            }
                            ?>
                            <select class="input-item selectinput" name="Language_modif">
                                <option value="<?php _e("français", "s1b"); ?>" <?php echo (($userLang == __("français", "s1b")) ? "selected" : ""); ?>><?php _e("français", "s1b"); ?></option>
                                <option value="<?php _e("anglais", "s1b"); ?>" <?php echo (($userLang == __("anglais", "s1b")) ? "selected" : ""); ?>><?php _e("anglais", "s1b"); ?></option>
                            </select>
                           <?php // if ($_SESSION['WPLANG'] <> "en_US") { ?>                                
                            
                        </td>
                    </tr>
                    <tr>
                        <td class="pef-label">
                            <?php _e("Sexe", "s1b"); ?>
                        </td>
                        <td>
                           <input <?php if ($user->getGender() == "male") echo "checked" ?> type="radio" name="Gender_modif" id="radio" value="male" /><label for="radio1"><?php _e("masculin", "s1b"); ?></label>
                           <br/>                                    
                           <input <?php if ($user->getGender() == "female") echo "checked" ?> type="radio" name="Gender_modif" id="radio" value="female" /><label for="radio1"><?php _e("féminin", "s1b"); ?></label>
                        </td>
                    </tr>
                </table>

                <?php _e("* champs obligatoires", "s1b"); ?>
                
                <div class="buttons-bar">
                    <div class="inner-padding">
                        <button class="float-right button bt-blue-m margin-right margin-left"><?php _e("Valider","s1b");?></button>
                        <?php if (!$_POST) {?>
                        <a class="button bt-black-xs float-right" href="javascript:history.back()" class="link"><?php _e("Annuler", "s1b") ?></a>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>            
    </div>
    <div id="content-right">
        <?php
        $userToolBox = new \Sb\View\Components\UserToolBox;
        echo $userToolBox->get();
        ?>
    </div>
</div>
<?php get_footer(); ?>