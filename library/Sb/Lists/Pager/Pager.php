<?php

/**
 * Contains the Pager class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  HTML
 * @package   Pager
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @author    Richard Heyes <richard@phpguru.org>
 * @copyright 2003-2008 Lorenzo Alberton, Richard Heyes
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: Pager.php,v 1.26 2008/02/02 16:55:04 quipo Exp $
 * @link      http://pear.php.net/package/Pager
 */
/**
 * Pager - Wrapper class for [Sliding|Jumping]-window Pager
 * Usage examples can be found in the PEAR manual
 *
 * @category  HTML
 * @package   Pager
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @author    Richard Heyes <richard@phpguru.org>
 * @copyright 2003-2008 Lorenzo Alberton, Richard Heyes
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Pager
 */

namespace Sb\Lists\Pager;

class Pager {
   
    /**
     * Return a pager based on $mode and $options
     *
     * @param array $options Optional parameters for the storage class
     *
     * @return object Storage object
     * @static
     * @access public
     */
    public static function factory($options = array()) {
        $mode = (isset($options['mode']) ? ucfirst($options['mode']) : 'Jumping');
        if ($mode == 'Jumping')
            $pager = new Jumping($options);
        else
            $pager = new Sliding($options);
        return $pager;
    }
}