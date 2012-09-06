<?php
/**
 * @version		1.0.0 townwizard_container $
 * @package		townwizard_container
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		MLS
 * @author mail	nobody@nobody.com
 *
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$isNew        = ($this->partnerLocation->id < 1);

$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );

JToolBarHelper::title(   JText::_( 'Partner Location' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::save('edit');

if ($isNew)  {
    JToolBarHelper::cancel();
} else {
    // for existing items the button is renamed `close`
    JToolBarHelper::cancel( 'cancel', 'Close' );
}

?>

<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Details' ); ?></legend>
        <table class="admintable">
            <tr>
                <td width="100" align="right" class="key">
                    <label for="partner">
                        <?php echo JText::_( 'Partner' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['partner_id'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'partner_id');?>
                </td>
            </tr>
            <tr>
                <td width="100" align="right" class="key">
                    <label for="street">
                        <?php echo JText::_( 'Street' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="street" id="street" size="32" maxlength="50" value="<?php echo $this->partnerLocation->street;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'street');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="city">
                        <?php echo JText::_( 'City' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="city" id="city" size="32" maxlength="50" value="<?php echo $this->partnerLocation->city;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'city');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="state">
                        <?php echo JText::_( 'State/Province' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="state" id="state" size="32" maxlength="50" value="<?php echo $this->partnerLocation->state;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'street');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="country">
                        <?php echo JText::_( 'Country' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="country" id="country" size="32" maxlength="50" value="<?php echo $this->partnerLocation->country;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'country');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="country">
                        <?php echo JText::_( 'Zip/Postcode' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="zip" id="zip" size="32" maxlength="50" value="<?php echo $this->partnerLocation->zip;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'zip');?>
                </td>
            </tr>
        </table>
    </fieldset>
        <fieldset class="adminform">
        	<legend><?php echo JText::_( 'Google Map' ); ?></legend>
            <table class="admintable">
            <tr>
                <td colspan="2">
                    <button type="button" onclick="geocode();">Find Coordinates By Address</button>&nbsp;
                    <button type="button" onclick="codeLatLng();">Find Address By Coordinates</button><br/>
                    <div id="sp_simple_map">
                        <script type="text/javascript">
                          var map;
                          var geocoder;
                          var marker;
                          function initialize() {
                                <?php if ($this->partnerLocation->latitude && $this->partnerLocation->longitude):?>
                                var
                                    myLatlng = new google.maps.LatLng(<?php echo $this->partnerLocation->latitude;?>, <?php echo $this->partnerLocation->longitude;?>);
                                <?php else:?>
                                var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
                                <?php endif;?>
                                var myOptions = {
                                  zoom: <?php echo ($this->partnerLocation->map_zoom ? $this->partnerLocation->map_zoom : 8);?>,
                                  center: myLatlng,
                                  mapTypeId: google.maps.MapTypeId.ROADMAP
                                };

                                map = new google.maps.Map(document.getElementById('sp_simple_map_canvas'),
                                    myOptions);

                                marker = new google.maps.Marker({
                                      position: myLatlng,
                                      map: map,
                                      title:"Location position"
                                });
                                document.getElementById('map_zoom').value = map.getZoom();
                                document.getElementById('latitude').value = marker.getPosition().lat();
                                document.getElementById('longitude').value = marker.getPosition().lng();
                                google.maps.event.addListener(map, 'click', function(event) {
                                    marker.setPosition(event.latLng);
                                    document.getElementById('latitude').value = event.latLng.lat();
                                    document.getElementById('longitude').value = event.latLng.lng();
                                    //codeLatLng();
                                });
                                google.maps.event.addListener(map, 'zoom_changed', function() {
                                    document.getElementById('map_zoom').value = map.getZoom();
                                });
                              geocoder = new google.maps.Geocoder();
                          }
                          function geocode() {
                              var street = document.getElementById('street').value;
                              var city = document.getElementById('city').value;
                              var state = document.getElementById('state').value;
                              var country = document.getElementById('country').value;
                              var zip = document.getElementById('zip').value;
                              var address = [];
                              if (street.length) {
                                  address.push(street);
                              }
                              if (city.length) {
                                  address.push(city);
                              }
                              if (state.length) {
                                  address.push(state);
                              }
                              if (country.length) {
                                  address.push(country);
                              }
                              if (zip.length) {
                                  address.push(zip);
                              }
                              address = address.join(', ');

                              geocoder.geocode( { address: address}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK && results.length) {
                                  // You should always check that a result was returned, as it is
                                  // possible to return an empty results object.
                                  if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                                    map.setCenter(results[0].geometry.location);
                                    marker.setPosition(results[0].geometry.location);

                                    document.getElementById('latitude').value = results[0].geometry.location.lat();
                                    document.getElementById('longitude').value = results[0].geometry.location.lng();
                                  }
                                } else {
                                  alert("Geocode was unsuccessful due to: " + status);
                                }
                              });
                          }
                          function codeLatLng() {
                              var lat = document.getElementById('latitude').value;
                              var lng = document.getElementById('longitude').value;
                              var latlng = new google.maps.LatLng(lat, lng);
                              geocoder.geocode({'latLng': latlng}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                  if (results[0]) {
                                      var address_component;
                                      var components_map = {'country': 'country',
                                                            'administrative_area_level_1': 'state',
                                                            'locality': 'city',
                                                            'route': 'street',
                                                            'postal_code': 'zip'};
                                      for (ci in results[0].address_components){
                                          address_component = results[0].address_components[ci];
                                          if (typeof address_component == "object"){
                                              var type = address_component.types[0];
                                              console.log(type);
                                              if (type in components_map){
                                                  document.getElementById(components_map[type]).value = address_component.long_name;
                                              }
                                          }
                                      }
                                  }
                                } else {
                                  alert("Geocoder failed due to: " + status);
                                }
                              });
                            }
                          google.maps.event.addDomListener(window, 'load', initialize);
                        </script>
                        <div id="sp_simple_map_canvas"></div>
                    </div>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="latitude">
                        <?php echo JText::_( 'Latitude' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="latitude" id="latitude" size="32" maxlength="50" value="<?php echo $this->partnerLocation->latitude;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'latitude');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="longitude">
                        <?php echo JText::_( 'Longitude' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="longitude" id="longitude" size="32" maxlength="50" value="<?php echo $this->partnerLocation->longitude;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'longitude');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="map_zoom">
                        <?php echo JText::_( 'Map Zoom' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="map_zoom" id="map_zoom" size="32" maxlength="50" value="<?php echo $this->partnerLocation->map_zoom;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerLocation, 'map_zoom');?>
                </td>
            </tr>
            </table>
        </fieldset>
    </div>

    <div class="clr"></div>

<input type="hidden" name="option" value="com_townwizard" />
<input type="hidden" name="controller" value="partnerlocation" />
<input type="hidden" name="id" value="<?php echo $this->partnerLocation->id; ?>" />
<input type="hidden" name="task" value="edit" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
?>