    </head>
    <body>     
        <div id="loading"><div id="loadingMsg"><?php _e("Chargement en cours...", "s1b");?></div></div>
        <!-- Debut div page -->
        <div id="page">
            <!-- Debut div header -->
            <div id="header">
                <div id="header-top">
                    <div class="header-inner">                        
                        <a href="<?php echo \Sb\Helpers\HTTPHelper::Link(""); ?>">
                            <img border="0" src="<?php echo $globalContext->getBaseUrl(); ?>Resources/images/logo.png"  />
                        </a>
                    </div>
                </div>
                <div id="header-bottom">
                    <div class="header-inner">
                        <div id="nav-main">
                            <?php
                            $searchForm = new \Sb\View\Components\SearchForm;
                            echo $searchForm->get();

                            if (!$globalContext->getConnectedUser()) {
                                $loginForm = new \Sb\View\Components\LoginForm;
                                echo $loginForm->get();
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Debut div content -->
            <div id="content">
                <?php
                if ($globalContext->getConnectedUser()) {
                    $userNavigation = new \Sb\View\Components\UserNavigation;
                    echo $userNavigation->get();
                }                
                ?>                
                <!-- Debut div content-wrap -->
                <div id="content-wrap">
