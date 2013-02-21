<?php

namespace Sb\Flash;
/*
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * Copyright 2011 Oracle and/or its affiliates. All rights reserved.
 *
 * Oracle and Java are registered trademarks of Oracle and/or its affiliates.
 * Other names may be trademarks of their respective owners.
 *
 * The contents of this file are subject to the terms of either the GNU
 * General Public License Version 2 only ("GPL") or the Common
 * Development and Distribution License("CDDL") (collectively, the
 * "License"). You may not use this file except in compliance with the
 * License. You can obtain a copy of the License at
 * http://www.netbeans.org/cddl-gplv2.html
 * or nbbuild/licenses/CDDL-GPL-2-CP. See the License for the
 * specific language governing permissions and limitations under the
 * License.  When distributing the software, include this License Header
 * Notice in each file and include the License file at
 * nbbuild/licenses/CDDL-GPL-2-CP.  Oracle designates this
 * particular file as subject to the "Classpath" exception as provided
 * by Oracle in the GPL Version 2 section of the License file that
 * accompanied this code. If applicable, add the following below the
 * License Header, with the fields enclosed by brackets [] replaced by
 * your own identifying information:
 * "Portions Copyrighted [year] [name of copyright owner]"
 *
 * If you wish your version of this file to be governed by only the CDDL
 * or only the GPL Version 2, indicate your decision by adding
 * "[Contributor] elects to include this software in this distribution
 * under the [CDDL or GPL Version 2] license." If you do not indicate a
 * single choice of license, a recipient has the option to distribute
 * your version of this file under either the CDDL, the GPL Version 2 or
 * to extend the choice of license to its licensees as provided above.
 * However, if you add GPL Version 2 code and therefore, elected the GPL
 * Version 2 license, then the option applies only if the new code is
 * made subject to such option by the copyright holder.
 *
 * Contributor(s):
 *
 * Portions Copyrighted 2011 Sun Microsystems, Inc.
 */

/**
 * Class managing flash messages.
 * <p>
 * (Flash messages are positive messages that are displayed exactly once,
 * on the next page; typically after form submitting.)
 */
class Flash {

    const FLASHES_KEY = '_flashes';

    private static $items = null;


    private function __construct() {
    }

    public static function hasItems() {
        self::initItems();
        return count(self::$items) > 0;
    }

    public static function addItem($message) {
        if (!strlen(trim($message))) {
            throw new \Exception('Cannot insert empty flash message.');
        }
        self::initItems();
        self::$items[] = $message;
    }

    /**
     * Get flash messages and clear them.
     * @return array flash messages
     */
    public static function getItems() {
        self::initItems();
        $copy = self::$items;
        self::$items = array();
        return $copy;
    }

    private static function initItems() {
        if (self::$items !== null) {
            return;
        }

        if (!array_key_exists(self::FLASHES_KEY, $_SESSION)) {
            $_SESSION[self::FLASHES_KEY] = array();
        }
        self::$items = &$_SESSION[self::FLASHES_KEY];
    }

    public static function showFlashes() {
       
        $flashes = null;
        // Récupération des messages flashes éventuels
        if (\Sb\Flash\Flash::hasItems()) {
            $flashes = \Sb\Flash\Flash::getItems();
            \Sb\Trace\Trace::addItem("Récupération des messages flashes");
        }

        $ret = "";
        if ($flashes) {
            $ret .= "<div id=\"flashes-wrap\"><div id=\"flashes-background\"></div><div id='flashes'><div id='flashes-close-button'></div><ul>";
            foreach ($flashes as $flash) {
                $ret .= "<li>" . $flash . "</li>";
            }
            $ret .= "</ul></div></div>";
        }
        echo $ret;
    }
}