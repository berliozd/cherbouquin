<?php
// Update Settings
if (isset($_REQUEST['submit'])) {
    if (!current_user_can('manage_options'))
        die(__('You cannot edit the search-by-category options.', "s1b"));

    $options = array();
    foreach (array_keys($_REQUEST) as $key) {
        $options[$key] = $_REQUEST[$key];
    }
    update_option("share1Book", $options);
}


if (!function_exists('getSafeFromArray')) {

    function getSafeFromArray($array, $key, $def) {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $def;
    }

}

// Get Current DB Values
$optionsFromDb = get_option("share1Book");
if ($optionsFromDb) {
    $googleBooksNbItemsSearched = getSafeFromArray($optionsFromDb, 'googleBooksNIS', 20);
    $googleBooksApiKey = getSafeFromArray($optionsFromDb, 'googleBooksAK', '');
    $amazonApiKey = getSafeFromArray($optionsFromDb, 'amazonAK', '');
    $amazonSecretKey = getSafeFromArray($optionsFromDb, 'amazonSK', '');
    $amazonAssociateTag = getSafeFromArray($optionsFromDb, 'amazonAT', '');
    $amazonNumberOfPageRequested = getSafeFromArray($optionsFromDb, 'amazonNOfPR', 2);

    $tracesEnabledChecked = "";
    $tracesEnabled = getSafeFromArray($optionsFromDb, 'tracesEnabled', false);
    if ($tracesEnabled)
        $tracesEnabledChecked = "checked";

    $logsEnabledChecked = "";
    $logsEnabled = getSafeFromArray($optionsFromDb, 'logsEnabled', false);
    if ($logsEnabled)
        $logsEnabledChecked = "checked";

    $searchNbResultsToShow = getSafeFromArray($optionsFromDb, 'searchNRTS', 15);
    $searchNbResultsPerPage = getSafeFromArray($optionsFromDb, 'searchNRPP', 3);
    $listNbBooksPerPage = getSafeFromArray($optionsFromDb, 'listNbBooksPerPage', 3);

    $cacheTemplateChecked = "";
    $cacheTemplate = getSafeFromArray($optionsFromDb, 'cacheTemplate', true);
    if ($cacheTemplate)
        $cacheTemplateChecked = "checked";

    $maxImportNb = getSafeFromArray($optionsFromDb, 'maxImportNb', 20);

    $userLibraryPageName = getSafeFromArray($optionsFromDb, 'userLibraryPageName', '');
    $friendLibraryPageName = getSafeFromArray($optionsFromDb, 'friendLibraryPageName', '');

    $facebookApiId = getSafeFromArray($optionsFromDb, 'facebookApiId', "");
    $facebookSecret = getSafeFromArray($optionsFromDb, 'facebookSecret', "");

    $maximumNbUserBooksForPublic = getSafeFromArray($optionsFromDb, 'maximumNbUserBooksForPublic', 300);

}
?>

<div class="wrap">
    <h2>Share1Book</h2>
    <form action="" method="post" id="your_plugin-config">
        <table class="form-table">
            <?php
            if (function_exists('wp_nonce_field')) {
                wp_nonce_field('your_plugin-updatesettings');
            }
            ?>
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Général</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Nombre de livres maximum par utilisateur (public)</th>
                <td><input type="text" name="maximumNbUserBooksForPublic" class="regular-text" value="<?php echo $maximumNbUserBooksForPublic; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Résulats de recherche</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Nombre minimum de résulats à afficher</th>
                <td><input type="text" name="searchNRTS" class="regular-text" value="<?php echo $searchNbResultsToShow; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top">Nombre de résulats par page</th>
                <td><input type="text" name="searchNRPP" class="regular-text" value="<?php echo $searchNbResultsPerPage; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Google books</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Api key</th>
                <td><input type="text" name="googleBooksAK" id="var1" class="regular-text" value="<?php echo $googleBooksApiKey; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top">Nb items recherchés (nb d'item maximum que renverra Google)</th>
                <td><input type="text" name="googleBooksNIS" id="var1" class="regular-text" value="<?php echo $googleBooksNbItemsSearched; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Amazon</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Api key</th>
                <td><input type="text" name="amazonAK" class="regular-text" value="<?php echo $amazonApiKey; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top">Secret key</th>
                <td><input type="text" name="amazonSK" class="regular-text" value="<?php echo $amazonSecretKey; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top">Associate tag</th>
                <td><input type="text" name="amazonAT" class="regular-text" value="<?php echo $amazonAssociateTag; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top">Nombre de page amazon requêtée lors des recherches</th>
                <td><input type="text" name="amazonNOfPR" class="regular-text" value="<?php echo $amazonNumberOfPageRequested; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Listes de livres</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Nombre de livres par page</th>
                <td><input type="text" name="listNbBooksPerPage" class="regular-text" value="<?php echo $listNbBooksPerPage; ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Import</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Nombre de livre autorisés lors de l'import</th>
                <td><input type="text" name="maxImportNb" class="regular-text" value="<?php echo $maxImportNb ?>"/></td>
            </tr>           
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Autres</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Traces activées</th>
                <td>
                    <input type="hidden" name="tracesEnabled" value="">
                    <input type="checkbox" name="tracesEnabled" <?php echo $tracesEnabledChecked ?>/>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">Logs activés</th>
                <td>
                    <input type="hidden" name="logsEnabled" value="">
                    <input type="checkbox" name="logsEnabled" <?php echo $logsEnabledChecked ?>/>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">Cache des templates activés</th>
                <td>
                    <input type="hidden" name="cacheTemplate" value="">
                    <input type="checkbox" name="cacheTemplate" <?php echo $cacheTemplateChecked ?>/>
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">Nom de la page de la bibliothèque de l'utilisateur (sa clé d'url)</th>
                <td><input type="text" name="userLibraryPageName" class="regular-text" value="<?php echo $userLibraryPageName ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top">Nom de la page de la bibliothèque pour les amis (sa clé d'url)</th>
                <td><input type="text" name="friendLibraryPageName" class="regular-text" value="<?php echo $friendLibraryPageName ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top" colspan="2"><b>Facebook</b></th>
            </tr>
            <tr>
                <th scope="row" valign="top">Api Id</th>
                <td><input type="text" name="facebookApiId" class="regular-text" value="<?php echo $facebookApiId ?>"/></td>
            </tr>
            <tr>
                <th scope="row" valign="top">Secret</th>
                <td><input type="text" name="facebookSecret" class="regular-text" value="<?php echo $facebookSecret ?>"/></td>
            </tr>
        </table>
        <br/>
        <span class="submit" style="border: 0;"><input type="submit" name="submit" value="Save Settings" /></span>
    </form>
</div>
