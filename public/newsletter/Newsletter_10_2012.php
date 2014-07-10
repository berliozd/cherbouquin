<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <!-- This is a simple example template that you can edit to create your own custom templates -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!-- Facebook sharing information tags -->
            <meta property="og:title" content="Newsletter Cherbouquin">

                <title>Newsletter Cherbouquin</title>
                <style type="text/css">
                    /* NOTE: CSS should be inlined to avoid having it stripped in certain email clients like GMail. 
            MailChimp automatically inlines CSS for you or you can use this tool: http://beaker.mailchimp.com/inline-css. */

                    /* Client-specific Styles */
                    #outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
                    body{width:100% !important;} /* Force Hotmail to display emails at full width */
                    body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */

                    /* Reset Styles */
                    body{margin:0; padding:0;}
                    img{border:none; font-size:14px; font-weight:bold; height:auto; line-height:100%; outline:none; text-decoration:none; text-transform:capitalize;}
                    #backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}

                    /* Template Styles */

                    /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: COMMON PAGE ELEMENTS /\/\/\/\/\/\/\/\/\/\ */

                    /**
                    * @tab Page
                    * @section background color
                    * @tip Set the background color for your email. You may want to choose one that matches your company's branding.
                    * @theme page
                    */
                    body, .backgroundTable{
                        /*@editable*/ background:url(http://www.cherbouquin.fr/images/newsletter/background.png);
                    }

                    /**
                    * @tab Page
                    * @section email border
                    * @tip Set the border for your email.
                    */
                    #templateContainer{
                        /*@editable*/ border: 1px solid #DDDDDD;
                    }

                    /**
                    * @tab Page
                    * @section heading 1
                    * @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
                    * @theme heading1
                    */
                    h1, .h1{
                        /*@editable*/ color:#239CD3;
                        display:block;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:26px;
                        /*@editable*/ font-weight:bold;
                        /*@editable*/ line-height:100%;
                        margin-bottom:10px;
                        /*@editable*/ text-align:left;
                    }

                    /**
                    * @tab Page
                    * @section heading 2
                    * @tip Set the styling for all second-level headings in your emails.
                    * @theme heading2
                    */
                    h2, .h2{
                        /*@editable*/ color:#239CD3;
                        display:block;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:20px;
                        /*@editable*/ font-weight:bold;
                        /*@editable*/ line-height:100%;
                        margin-bottom:10px;
                        /*@editable*/ text-align:left;
                    }

                    /**
                    * @tab Page
                    * @section heading 3
                    * @tip Set the styling for all third-level headings in your emails.
                    * @theme heading3
                    */
                    h3, .h3{
                        /*@editable*/ color:#239CD3;
                        display:block;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:16px;
                        /*@editable*/ font-weight:bold;
                        /*@editable*/ line-height:100%;
                        margin-bottom:10px;
                        /*@editable*/ text-align:left;
                    }

                    /**
                    * @tab Page
                    * @section heading 4
                    * @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
                    * @theme heading4
                    */
                    h4, .h4{
                        /*@editable*/ color:#239CD3;
                        display:block;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:14px;
                        /*@editable*/ font-weight:bold;
                        /*@editable*/ line-height:100%;
                        margin-bottom:10px;
                        /*@editable*/ text-align:left;
                    }

                    /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: PREHEADER /\/\/\/\/\/\/\/\/\/\ */

                    /**
                    * @tab Header
                    * @section preheader style
                    * @tip Set the background color for your email's preheader area.
                    * @theme page
                    */
                    #templatePreheader{
                        /*@editable*/ background-color:#FAFAFA;
                    }

                    /**
                    * @tab Header
                    * @section preheader text
                    * @tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
                    */
                    .preheaderContent div{
                        /*@editable*/ color:#505050;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:10px;
                        /*@editable*/ line-height:100%;
                        /*@editable*/ text-align:left;
                    }

                    /**
                    * @tab Header
                    * @section preheader link
                    * @tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
                    */
                    .preheaderContent div a:link, .preheaderContent div a:visited{
                        /*@editable*/ color:#336699;
                        /*@editable*/ font-weight:normal;
                        /*@editable*/ text-decoration:underline;
                    }

                    .preheaderContent div img{
                        height:auto;
                        max-width:600px;
                    }

                    /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: HEADER /\/\/\/\/\/\/\/\/\/\ */

                    /**
                    * @tab Header
                    * @section header style
                    * @tip Set the background color and border for your email's header area.
                    * @theme header
                    */
                    #templateHeader{
                        /*@editable*/ background-color:#FFFFFF;
                        /*@editable*/ border-bottom:0;
                    }

                    /**
                    * @tab Header
                    * @section header text
                    * @tip Set the styling for your email's header text. Choose a size and color that is easy to read.
                    */
                    /**
                    * @tab Header
                    * @section header text
                    * @tip Set the styling for your email's header text. Choose a size and color that is easy to read.
                    */
                    .headerContent{
                        /*@editable*/ color:#202020;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:34px;
                        /*@editable*/ font-weight:bold;
                        /*@editable*/ line-height:100%;
                        /*@editable*/ padding:0;
                        /*@editable*/ text-align:center;
                        /*@editable*/ vertical-align:middle;
                    }

                    /**
                    * @tab Header
                    * @section header link
                    * @tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
                    */
                    .headerContent a:link, .headerContent a:visited{
                        /*@editable*/ color:#336699;
                        /*@editable*/ font-weight:normal;
                        /*@editable*/ text-decoration:underline;
                    }

                    #headerImage{
                        height:auto;
                        max-width:600px !important;
                    }

                    /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: MAIN BODY /\/\/\/\/\/\/\/\/\/\ */

                    /**
                    * @tab Body
                    * @section body style
                    * @tip Set the background color for your email's body area.
                    */
                    #templateContainer, .bodyContent{
                        /*@editable*/ background-color:#FDFDFD;
                    }

                    /**
                    * @tab Body
                    * @section body text
                    * @tip Set the styling for your email's main content text. Choose a size and color that is easy to read.
                    * @theme main
                    */
                    .bodyContent div{
                        /*@editable*/ color:#505050;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:12px;
                        /*@editable*/ line-height:150%;
                        /*@editable*/ text-align:left;
                    }

                    /**
                    * @tab Body
                    * @section body link
                    * @tip Set the styling for your email's main content links. Choose a color that helps them stand out from your text.
                    */
                    .bodyContent div a:link, .bodyContent div a:visited{
                        /*@editable*/ color:#336699;
                        /*@editable*/ font-weight:normal;
                        /*@editable*/ text-decoration:underline;
                    }

                    .bodyContent img{
                        display:inline;
                        margin-bottom:10px;
                    }

                    /* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: FOOTER /\/\/\/\/\/\/\/\/\/\ */

                    /**
                    * @tab Footer
                    * @section footer style
                    * @tip Set the background color and top border for your email's footer area.
                    * @theme footer
                    */
                    #templateFooter{
                        /*@editable*/ background-color:#FDFDFD;
                        /*@editable*/ border-top:0;
                    }

                    /**
                    * @tab Footer
                    * @section footer text
                    * @tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
                    * @theme footer
                    */
                    .footerContent div{
                        /*@editable*/ color:#707070;
                        /*@editable*/ font-family:Verdana;
                        /*@editable*/ font-size:12px;
                        /*@editable*/ line-height:125%;
                        /*@editable*/ text-align:left;
                    }

                    /**
                    * @tab Footer
                    * @section footer link
                    * @tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
                    */
                    .footerContent div a:link, .footerContent div a:visited{
                        /*@editable*/ color:#336699;
                        /*@editable*/ font-weight:normal;
                        /*@editable*/ text-decoration:underline;
                    }

                    .footerContent img{
                        display:inline;
                    }

                    /**
                    * @tab Footer
                    * @section social bar style
                    * @tip Set the background color and border for your email's footer social bar.
                    */
                    #social{
                        /*@editable*/ background-color:#C0C0C0;
                        /*@editable*/ border:1px solid #F5F5F5;
                    }

                    /**
                    * @tab Footer
                    * @section social bar style
                    * @tip Set the background color and border for your email's footer social bar.
                    */
                    #social div{
                        /*@editable*/ text-align:center;
                    }

                    /**
                    * @tab Footer
                    * @section utility bar style
                    * @tip Set the background color and border for your email's footer utility bar.
                    */
                    #utility{
                        /*@editable*/ background-color:#FDFDFD;
                        /*@editable*/ border-top:1px solid #F5F5F5;
                    }

                    /**
                    * @tab Footer
                    * @section utility bar style
                    * @tip Set the background color and border for your email's footer utility bar.
                    */
                    #utility div{
                        /*@editable*/ text-align:center;
                    }

                    #monkeyRewards img{
                        max-width:160px;
                    }
                </style>
                </head>
                <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
                    <center>

                        <br></br>

                        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="templateContainer">
                                        <tr>
                                            <td align="center" valign="top">
                                                </br>
                                                <div style="font-family:Verdana;color:#707070;font-size:12px;">
                                                    Si vous rencontrez des difficultés pour visualiser cet email, cliquez 
                                                    <a href="http://www.cherbouquin.fr/newsletter/Newsletter_10_2012.php" style="color:#336699">Ici</a>
                                                </div>
                                                </br>

                                                <!-- // Begin Template Header \\ -->
                                                <table border="0" cellpadding="0" cellspacing="0" width="600" id="templateHeader">
                                                    <tr>
                                                        <td class="headerContent">
                                                            <!-- // Begin Module: Standard Header Image \\ -->
                                                            <a href="http://www.cherbouquin.fr/">
                                                                <img src="http://www.cherbouquin.fr/images/newsletter/header_650_100_px.jpg" style="width:650px;border" id="headerImage campaign-icon" mc:label="header_image" mc:edit="header_image" mc:allowdesigner="" mc:allowtext=""/>
                                                            </a>
                                                            <!-- // End Module: Standard Header Image \\ -->
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- // End Template Header \\ -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- // Begin Template Body \\ -->
                                                <table border="0" cellpadding="10" cellspacing="0" width="600" id="templateBody">
                                                    <tr>
                                                        <td valign="top" class="bodyContent">
                                                            <!-- // Begin Module: Standard Content \\ -->
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                                </br>
                                                                <span class="h1">Dix livres qui font la Une</span>
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/cinquante-nuances-de-grey/e-l-james/653/" title="Cinquante nuances de Grey de E.L. James"><img src="http://www.cherbouquin.fr/images/covers/bid653.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/lhiver-du-monde/ken-follett--dominique-haas--odile-demange/46/" title="L'hiver du monde de Ken Follett"><img src="http://www.cherbouquin.fr/images/covers/bid46.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/largo-winch--tome-18--colere-rouge/jean-van-hamme/1141/" title="Colère rouge de Philippe Francq et Jean Van Hamme"><img src="http://www.cherbouquin.fr/images/covers/bid1141.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/la-maison-da-cote/lisa-gardner/1325/" title="La maison d'à côté de Lisa Gardner "><img src="http://www.cherbouquin.fr/images/covers/bid1325.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/une-place-a-prendre/j-k-rowling/541/" title="Une place à prendre de J.K. Rowling"><img src="http://www.cherbouquin.fr/images/covers/bid541.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/atomka/franck-thilliez/920/" title="Atomka de Franck Thilliez"><img src="http://www.cherbouquin.fr/images/covers/bid920.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/le-cycle-de-cyann--tome-5--les-couloirs-de-lentretemps/francois-bourgeon--claude-lacroix/1327/" title="Les couloirs de l'Entretemps de Claude Lacroix et François Bourgeon"><img src="http://www.cherbouquin.fr/images/covers/bid1327.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/remede-mortel/harlan-coben/1329/" title="Remède mortel d'Harlan Coben"><img src="http://www.cherbouquin.fr/images/covers/bid1329.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/14/jean-echenoz/916/" title="14 de Jean Echenoz"><img src="http://www.cherbouquin.fr/images/covers/bid916.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/la-frondeuse/alix-bouilhaguet--christophe-jakubyszyn/1331/" title="La frondeuse de Christophe Jakubyszyn et Alix Bouilhaguet"><img src="http://www.cherbouquin.fr/images/covers/bid1331.jpg" style="width:90px;height:130px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            </br>
                                                            <div style="margin-left:50px;border: 1px solid rgb(239,239,239);width:500px;"></div>
                                                            </br>

                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                                <tr>
                                                                    <a href="http://www.cherbouquin.fr/amis/inviter/" title="inviter vos amis"><img src="http://www.cherbouquin.fr/images/newsletter/invitation_cherbouquin.jpg" mc:label="image" mc:edit="liwc600_image00" style="margin-left:175px"/></a>
                                                                </tr>
                                                            </table>

                                                            </br></br>

                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                                <span class="h1">A paraître en novembre</span>
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/pretty-little-secrets/sara-shepard/1225/" title="Pretty little secrets de Sara Shepard"><img src="http://www.cherbouquin.fr/images/covers/bid1225.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/behemoth/peter-watts/1222/" title="Behemoth de Peter Watts"><img src="http://www.cherbouquin.fr/images/covers/bid1222.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/xiii--tome-21--lappat/jean-van-hamme--yves-sente/1142/" title="XIII, tome 21 : l'appät de Jean Van Hamme, Yves Sente"><img src="http://www.cherbouquin.fr/images/covers/bid1142.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/la-ou-vous-ne-serez-pas/castellanos-moya-h/1129/" title="Là où vous ne serez pas d'Horatio Castellanos Moya"><img src="http://www.cherbouquin.fr/images/covers/bid1129.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/timeville/tim-sliders/1123/" title="Timeville de Tim Sliders"><img src="http://www.cherbouquin.fr/images/covers/bid1123.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/90-jours/bill-clegg--laure-manceau/1104/" title="90 jours de Bill Clegg"><img src="http://www.cherbouquin.fr/images/covers/bid1104.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/nouvelles-du-pays/atta-sefi/1102/" title="Nouvelles du Pays d'Atta Sefi"><img src="http://www.cherbouquin.fr/images/covers/bid1102.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            </br></br>

                                                            <!-- // Begin Module: Left Image with Content \\ -->
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" mc:repeatable mc:variant="content with left image">
                                                                <span class="h1">Dernières critiques sur Cherbouquin</span>
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/luxe---co--comment-les-marques-ont-tue-le-luxe/dana-thomas/54/"><img src="http://www.cherbouquin.fr/images/covers/bid54.jpg" style="width:55px;height:80px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td valign="top">
                                                                        <div mc:edit="liwc600_content00">
                                                                            <a href="http://www.cherbouquin.fr/livre/luxe---co--comment-les-marques-ont-tue-le-luxe/dana-thomas/54/"><strong>Luxe & Co : Comment les marques ont tué le luxe de Dana Thomas</strong></a>
                                                                            </br>
                                                                            Super intéressant, pleins d'anecdotes sur l'évolution du luxe sur ces 30 dernières années... C'est un peu l'histoire de comment le luxe est devenu mass market ...
                                                                        </div>
                                                                        </br></br>
                                                                        <div style="border: 1px solid rgb(239,239,239);width:500px;"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" mc:repeatable mc:variant="content with left image">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/un-chagrin-de-passage/francoise-sagan/666/"><img src="http://www.cherbouquin.fr/images/covers/bid666.jpg" style="width:55px;height:80px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td valign="top">
                                                                        <div mc:edit="liwc600_content00">
                                                                            <a href="http://www.cherbouquin.fr/livre/un-chagrin-de-passage/francoise-sagan/666/"><strong>Un chagrin de passage de Françoise Sagan</strong></a>
                                                                            </br>
                                                                            Réflexion sur l'état d'esprit d'un homme à qui on annonce sa mort prochaine. La fin est surprenante
                                                                        </div>
                                                                        </br></br>
                                                                        <div style="border: 1px solid rgb(239,239,239);width:500px;"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" mc:repeatable mc:variant="content with left image">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/les-quatre-coins-du-monde--tome-1/hugues-labiano/835/"><img src="http://www.cherbouquin.fr/images/covers/bid835.jpg" style="width:55px;height:80px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td valign="top">
                                                                        <div mc:edit="liwc600_content00">
                                                                            <a href="http://www.cherbouquin.fr/livre/les-quatre-coins-du-monde--tome-1/hugues-labiano/835/"><strong>Les quatre coins du monde, tome 1 de Hugues Labiano</strong></a>
                                                                            </br>
                                                                            Peu avant la Première Guerre Mondiale, un jeune officier français décide de rejoindre un détachement nomade dans le Sahara. Un Tome 1 qui pose superbement les acteurs et les lieux.
                                                                        </div>
                                                                        </br>
                                                                        <div style="border: 1px solid rgb(239,239,239);width:500px;"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" mc:repeatable mc:variant="content with left image">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/lhomme-qui-rit/victor-hugo/148/"><img src="http://www.cherbouquin.fr/images/covers/bid148.jpg" style="width:55px;height:80px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td valign="top">
                                                                        <div mc:edit="liwc600_content00">
                                                                            <a href="http://www.cherbouquin.fr/livre/lhomme-qui-rit/victor-hugo/148/"><strong>L'homme qui rit de Victor Hugo</strong></a>
                                                                            </br>
                                                                            C'est une formidable épopée, mêlant l'idylle, l'apocalypse et la fantaisie, aux images et au vocabulaire éblouissants, partagée entre l'ombre et la lumière, le bien et le mal, l'ironie et l'humour noir.
                                                                        </div>
                                                                        </br>
                                                                        <div style="border: 1px solid rgb(239,239,239);width:500px;"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" mc:repeatable mc:variant="content with left image">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <a href="http://www.cherbouquin.fr/livre/une-certaine-idee-du-bonheur/rachel-kadish/676/"><img src="http://www.cherbouquin.fr/images/covers/bid676.jpg" style="width:55px;height:80px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                    </td>
                                                                    <td valign="top">
                                                                        <div mc:edit="liwc600_content00">
                                                                            <a href="http://www.cherbouquin.fr/livre/une-certaine-idee-du-bonheur/rachel-kadish/676/"><strong>Une certaine idée du bonheur de Rachel Kadish</strong></a>
                                                                            </br>
                                                                            Gros coup de coeur pour ce roman américain qui s'interroge sur le bonheur entre réalité et littérature avec un petit côté David Lodge qui m'a ravie!
                                                                        </div>
                                                                        </br></br>
                                                                        <div style="border: 1px solid rgb(239,239,239);width:500px;"></div>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            </br></br>

                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" mc:repeatable mc:variant="content with left image">
                                                                <span class="h1">Découvrez des blogueurs littéraires</span>
                                                                <tr>
                                                                    <td class="headerContent">
                                                                        <div mc:edit="liwc600_content00">
                                                                            <a href="http://mespetitesidees.wordpress.com/"><img src="http://www.cherbouquin.fr/images/newsletter/mes_petites_idees.jpg" style="max-width:500px;max-height:125px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top">
                                                                        <div mc:edit="liwc600_content00" style="width:500px;">
                                                                            <strong>Chronique de <a href="http://mespetitesidees.wordpress.com/">Mes petites idées</a> :  Rêves oubliés de Leonor De Récondo</strong>
                                                                            </br>
                                                                            Ce livre nous relate la fuite de la guerre d’Espagne puis du franquisme d’une famille. Aïta, père de famille, est responsable d’une fabrique de céramique. Au moment de sa fuite sa famille se trouve à Irùn depuis déjà plusieurs semaines ...
                                                                            <a href="http://mespetitesidees.wordpress.com/2012/08/17/reves-oublies-de-leonor-de-recondo/">Lire la suite</a>
                                                                        </div>
                                                                    </td>
                                                                    <td align="right" valign="top">
                                                                        <a href="http://mespetitesidees.wordpress.com/2012/08/17/reves-oublies-de-leonor-de-recondo/">
                                                                            <img src="http://www.cherbouquin.fr/images/covers/bid1337.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            
                                                            <div style="border: 1px solid rgb(239,239,239);width:500px;"></div>
                                                            </br>

                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" mc:repeatable mc:variant="content with left image">
                                                                <tr>
                                                                    <td class="headerContent">
                                                                        <div mc:edit="liwc600_content00">
                                                                            <a href="http://mapetitebibliotheque.wordpress.com/"><img src="http://www.cherbouquin.fr/images/newsletter/ma_petite_bibliotheque.png" style="max-width:500px;max-height:125px" mc:label="image" mc:edit="liwc600_image00"/></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top">
                                                                        <div mc:edit="liwc600_content00" style="width:500px;">
                                                                            <strong>Chronique de <a href="http://mapetitebibliotheque.wordpress.com/">Ma petite bibliothèque</a> :  Complétement cramé de Gilles Legardinier</strong>
                                                                            </br>
                                                                            Complétement cramé est un vrai régal à lire. Gilles Legardinier nous plonge dans le changement de vie de Andrew Blake, directeur d’entreprise n’ayant plus le goût de vivre suite à la mort de sa femme quelques années auparavant, entre autres.
                                                                            <a href="http://mapetitebibliotheque.wordpress.com/2012/10/26/completement-crame-de-gilles-legardinier/">Lire la suite</a>
                                                                        </div>
                                                                    </td>
                                                                    <td align="right" valign="top">
                                                                        <a href="http://mapetitebibliotheque.wordpress.com/2012/10/26/completement-crame-de-gilles-legardinier/">
                                                                            <img src="http://www.cherbouquin.fr/images/covers/bid995.jpg" style="width:70px;height:100px" mc:label="image" mc:edit="liwc600_image00"/>   
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!-- // End Module: Left Image with Content \\ -->
                                                            <!-- // End Module: Standard Content \\ -->
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- // End Template Body \\ -->
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- // Begin Template Footer \\ -->
                                                <table border="0" cellpadding="10" cellspacing="0" width="600" id="templateFooter">
                                                    <tr>
                                                        <td valign="top" class="footerContent">

                                                            <!-- // Begin Module: Standard Footer \\ -->
                                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                                <tr>
                                                                    <td colspan="2" valign="middle" id="social">
                                                                        <div mc:edit="std_social">
                                                                            <em>Suivez nous sur</em>
                                                                            &nbsp;<a href="https://twitter.com/cherbouquin">Twitter</a> | <a href="http://www.facebook.com/CherBouquin">Facebook</a> | <a href="http://pinterest.com/cherbouquin/">Pinterest</a>&nbsp;
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" width="600">
                                                                        <br></br>
                                                                        <div mc:edit="std_footer">
                                                                            <em>Copyright &copy; 2012 O.R.D.B</em>
                                                                            <br></br>
                                                                            <em>Vous recevez cette newsletter suite à votre inscription sur le site Cherbouquin.fr</em>
                                                                            <br></br>
                                                                            <em>Si vous souhaitez vous désabonner, connectez-vous à Cherbouquin et sur votre profil modifiez </em>
                                                                            <a href="http://www.cherbouquin.fr/profil-membre/parametrage/">vos paramètres</a>
                                                                        </div>
                                                                        <br></br>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2">
                                                                        <div mc:edit="std_utility">
                                                                            <em>O.R.D.B : 29 rue de Trévise, 75009 Paris</em>
                                                                            <br></br>
                                                                            <em>Récépissé CNIL n°1618314</em>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!-- // End Module: Standard Footer \\ -->

                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- // End Template Footer \\ -->
                                            </td>
                                        </tr>
                                    </table>
                                    <br></br>
                                </td>
                            </tr>
                        </table>
                    </center>
                </body>
                </html>