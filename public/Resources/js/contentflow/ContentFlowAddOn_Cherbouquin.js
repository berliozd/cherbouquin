/*  ContentFlowAddOn_DEFAULT, version 1.0.2 
 *  (c) 2008 - 2010 Sebastian Kutsch
 *  <http://www.jacksasylum.eu/ContentFlow/>
 *
 *  This file is distributed under the terms of the MIT license.
 *  (see http://www.jacksasylum.eu/ContentFlow/LICENSE)
 */

/*
 * This is an example file of an AddOn file and will not be used by ContentFlow.
 * All values are the default values of ContentFlow.
 *
 * To create a new AddOn follow this guideline:
 *              (replace ADDONNAME by the name of your AddOn)
 *
 * 1. rename this file to ContentFlowAddOn_ADDONNAME.js
 * 2. Change the string 'DEFAULT' in the 'new ContentFlowAddOn' line to 'ADDONNAME'
 * 3. Make the changes you like/need
 * 4. Remove all settings you do not need (or comment out for testing).
 * 5. Add 'ADDONNAME' to the load attribute of the ContentFlow script tag in your web page
 * 6. Reload your page :-)
 *
 */
new ContentFlowAddOn ('Cherbouquin', {
    /*
     * ContentFlow configuration.
     * Will overwrite the default configuration (or configuration of previously loaded AddOns).
     * For a detailed explanation of each value take a look at the documentation.
     */
    ContentFlowConf: {
        scaleFactor: 1.3,               // overall scale factor of content
        relativeItemPosition: "center center", // align top/above, bottom/below, left, right, center of position coordinate
        visibleItems: 4,               // how man item are visible on each side (-1 := auto)        
        reflectionHeight: 0.2,          // float (relative to original image height)
        reflectionGap: 0.015,             // gap between the image and the reflection
        space: 0.1,
        shownItems : 9
    },
    
    calcCoordinates: function (item) {
           var rP = item.relativePosition;
           var c = this.getAddOnConf('Cherbouquin');
           var w = item.size.width;
           //if (this.conf.verticalFlow) w = item.size.height;
           var x = rP*w/2*(1 + c.space) *this.conf.scaleFactor - w* (c.shownItems % 2 ? 0 : 0.5) / 1.4;
           if (this.conf.verticalFlow) x *= 2*2/3;
           var y = 0;

           return {x: x, y: y};
       }

});
