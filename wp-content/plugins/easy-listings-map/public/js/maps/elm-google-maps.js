(function( $, window ) {
    var map = infoBubble = markerClusterer = animatedMarker = null;
    var get_markers = true;    // Flag for checking should map get markers on bound changes or not
    var markers = [];          // Array of markers in the map.

    /**
     * Initialize Google Maps for listings
     * @return void
     */
    window.initializeListingMap = function initializeListingMap() {
        // If map initialized already don't init again.
        if ( map ) {
            return;
        }

        var mapTypeIds = [];
        for ( i = 0, max = elm_google_maps.map_types.length; i < max; i++ ) {
            if ( 'ROADMAP' === elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.ROADMAP );
            } else if ( 'SATELLITE' ===  elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.SATELLITE );
            } else if ( 'HYBRID' === elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.HYBRID );
            } else if ( 'TERRAIN' === elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.TERRAIN );
            }
        }

        if ( ! mapTypeIds.length ) {
            mapTypeIds.push( google.maps.MapTypeId.ROADMAP );
        }

        var latlng = new google.maps.LatLng( elm_google_maps.default_latitude, elm_google_maps.default_longitude );
        var map_options = {
            zoom: parseInt( elm_google_maps.zoom ),
            center: latlng,
            mapTypeControl: true,
            scrollwheel: false,
            mapTypeControlOptions: {
              style: google.maps.MapTypeControlStyle.DEFAULT,
              mapTypeIds: mapTypeIds
            },
            zoomControl: true,
            zoomControlOptions: {
              style: google.maps.ZoomControlStyle.DEFAULT,
              position: google.maps.ControlPosition.RIGHT_TOP
            },
            streetViewControlOptions: {
              position: google.maps.ControlPosition.RIGHT_TOP
            }
        };
        if ( elm_google_maps.map_styles.length ) {
          map_options.styles = jQuery.parseJSON( elm_google_maps.map_styles );
        }
        map = new google.maps.Map( document.getElementById( elm_google_maps.map_id ), map_options );

        // Setting default map type if exists.
        if ( typeof elm_google_maps.default_map_type != 'undefined' ) {
          map.setMapTypeId( google.maps.MapTypeId[ elm_google_maps.default_map_type ] );
        }

        infoBubble = generateInfoBubble();

        var properties = elm_google_maps.markers;
        properties = jQuery.parseJSON( properties );
        if ( properties.length ) {
            createMarkers( properties );
            // Adding map markers to clusters.
            addMarkersToCluster();
        }
        /**
         * Auto zoom enabled.
         * Auto zoom and fitBounds to showing all of markers as good as possible.
         */
        if ( '1' === elm_google_maps.auto_zoom && markers.length ) {
            mapAutoZoom();
        }
        // Auto zoom disabled.
        else if ( markers.length ) {
            // Setting map center to first marker position.
            map.setCenter( markers[0].position );
        }

        // Load bound markers if all of listings doesn't loads already.
        if ( typeof elm_google_maps.zoom_events != 'undefined' && 0 != elm_google_maps.zoom_events ) {
            google.maps.event.addListener( map, 'dragend', getBoundMarkers );
            google.maps.event.addListener( map, 'zoom_changed', getBoundMarkers );
        }

        google.maps.event.addListener( map, 'tilesloaded', function() {
          jQuery('#gmap-loading').remove();
        });
    }

    if ( 'object' === typeof google && 'object' === typeof google.maps ) {
        $( function() {
            if ( $( '#' + elm_google_maps.map_id ).is( ':visible' ) ) {
                initializeListingMap();
            } else {
                // Map is inside bootstrap tabs so init map when it is visible.
                $( 'a[data-toggle="tab"]' ).on('shown shown.bs.tab', function ( e ) {
                    if ( $( '#' + elm_google_maps.map_id ).is( ':visible' ) ) {
                        initializeListingMap();
                    }
                });
                // Map is inside jQuery or zozoui tabs so init map when it is visible.
                $( 'ul.ui-tabs-nav li, ul.z-tabs-nav li' ).on( 'click', function( e ) {
                    if ( $( '#' + elm_google_maps.map_id ).is( ':visible' ) ) {
                        initializeListingMap();
                    }
                });
            }
        });
        google.maps.event.addDomListener(window, 'resize', function() {
            if ( map ) {
                var center = map.getCenter();
                google.maps.event.trigger(map, "resize");
                map.setCenter(center);
            }
        });
    }

    /**
     * Creating infowindow for property.
     * @param  google.maps.Marker marker
     * @param  {} property object of properties that are in the same coordinates.
     * @return void
     */
    window.getInfoWindow = function getInfoWindow( marker, property ) {
        return function() {
            var infoBubblePosition = infoBubble.get( 'position' );
            // Generate content of infobubble if it isn't defined already or previous infoBubble marker is not same as current marker.
            if ( typeof infoBubblePosition === 'undefined' ||
                ( typeof infoBubblePosition !== 'undefined' && marker.position.lat() !== infoBubblePosition.lat() &&
                    marker.position.lng() !== infoBubblePosition.lng() ) ) {
              infoBubble.close();
              // Creating a new infoBubble in order to not over writing on previous infoBubble content.
              infoBubble = generateInfoBubble();
              var content = '';
              if ( property.info.length > 1 ) {
                // Generating content for each property( properties that are in same coordinates ) in info window.
                for ( i = 0, max = property.info.length; i < max; i++ ) {
                  content = '<div class="property-infobubble-content">' +
                    '<img width="16" height="16" style="position: absolute; right: 0; margin-right: 8px; cursor: pointer; width: 16px; height: 16px;" onclick="infoBubble.close();" src="' + elm_google_maps.info_window_close + '">' +
                    '<a href="' + decodeURIComponent( property.info[i].url ) + '"><img src="' + property.info[0].image_url + '" width="300" height="150" class="elm-infobubble-image wp-post-image" /></a>' +
                    '<div class="title"><a class="infobubble-property-title" href="' + decodeURIComponent( property.info[i].url ) + '">' + property.info[i].title + '</a></div>' +
                    '<div class="property-type-status">' + property.info[i].property_type + ' - ' + property.info[i].property_status + '</div>' +
                    // '<div class="property-meta pricing">' +  property.info[i].price + '</div>' +
                    '<div class="property-feature-icons epl-clearfix">' + property.info[i].icons + '</div>' +
                    '</div>';
                  infoBubble.addTab( property.info[i].tab_title, content );
                }
              } else {
                content = '<div class="property-infobubble-content">' +
                  '<img width="16" height="16" style="position: absolute; right: 0; margin-right: 8px; cursor: pointer; width: 16px; height: 16px;" onclick="infoBubble.close();" src="' + elm_google_maps.info_window_close + '">' +
                  '<a href="' + decodeURIComponent( property.info[0].url ) + '"><img src="' + property.info[0].image_url + '" width="300" height="150" class="elm-infobubble-image wp-post-image" /></a>' +
                  '<div class="title"><a class="infobubble-property-title" href="' + decodeURIComponent( property.info[0].url ) + '">' + property.info[0].title + '</a></div>' +
                  '<div class="property-type-status">' + property.info[0].property_type + ' - ' + property.info[0].property_status + '</div>' +
                  // '<div class="property-meta pricing">' +  property.info[0].price + '</div>' +
                  '<div class="property-feature-icons epl-clearfix">' + property.info[0].icons + '</div>' +
                  '</div>';
                infoBubble.setContent( content );
              }
            }
            if ( ! infoBubble.isOpen() ) {
              infoBubble.open( map, marker );
            }
        }
    }

    /**
     * Getting markers from server when bounds of map changes.
     *
     * @since  1.0.0
     * @return void
     */
    window.getBoundMarkers = function getBoundMarkers() {
        /**
         * Checking should this function get bound markers or not.
         * Don't get markers when auto zoom changes zoom level.
         */
        if ( ! get_markers ) {
            get_markers = true;
            return;
        }
        // First, determine the map bounds
        var bounds = map.getBounds();
        // Then the points
        var swPoint = bounds.getSouthWest();
        var nePoint = bounds.getNorthEast();

        // Now, each individual coordinate
        var swLat = swPoint.lat();
        var swLng = swPoint.lng();
        var neLat = nePoint.lat();
        var neLng = nePoint.lng();

        jQuery.ajax({
            type: 'POST',
            url : elmPublicAjaxUrl,
            data : {
              'action'       : 'load_map_markers',
              'nonce'        : elm_google_maps.nonce,
              'southWestLat' : swLat,
              'southWestLng' : swLng,
              'northEastLat' : neLat,
              'northEastLng' : neLng,
              'query_vars'   : elm_google_maps.query_vars,
              'cluster_size' : elm_google_maps.cluster_size
            }
        })
        .done( function( response ) {
            response = jQuery.parseJSON( response );
            if ( 1 === response.success ) {
                addListingsToMap( response.markers );
            } else if ( 0 === response.success ) {
                console.log( response.message );
            }
        });
    }

    /**
     * Removing markers from map.
     *
     * @since  1.0.0
     * @return void
     */
    window.removeMarkers = function removeMarkers() {
        if ( markerClusterer ) {
            markerClusterer.clearMarkers();
        }
        // Resetting markers of map.
        markers = [];
    }

    /**
     * Creating markers for properties.
     *
     * @since  1.0.0
     * @param  [] properties
     * @return void
     */
    window.createMarkers = function createMarkers( properties ) {
        if ( properties.length ) {
            var marker;
            for ( i = 0, max = properties.length; i < max; i++ ) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng( properties[i].latitude, properties[i].longitude ),
                    icon: properties[i]['marker_icon'],
                    listing_id : properties[i]['listing_id']
                });
                markers.push( marker );
                google.maps.event.addListener( marker, 'click', getInfoWindow( marker, properties[i] ) );
            }
        }
    }

    /**
     * Creating clusters for markers of map.
     *
     * @since 1.0.0
     */
    window.addMarkersToCluster = function addMarkersToCluster() {
        if ( markers.length ) {
            var gridSize = elm_google_maps.cluster_size == -1 ? null : parseInt( elm_google_maps.cluster_size, 10 );
            markerClusterer = new MarkerClusterer(map, markers, {
                ignoreHidden:true,
                maxZoom: 14,
                gridSize: gridSize,
                styles: elm_google_maps.cluster_style
            });

            google.maps.event.addListener(markerClusterer, 'click', function(clusterer) {
                console.log( clusterer.getMarkers() );
            });
        }
    }

    /**
     * Adding listings to the map.
     *
     * @since 1.2.0
     * @param void listings
     */
    window.addListingsToMap = function addListingsToMap( listings ) {
        // Removing old markers.
        removeMarkers();
        if ( listings.length ) {
            // Creating markers.
            createMarkers( listings );
            // Adding map markers to clusters.
            addMarkersToCluster();
        }
    }

    /**
     * Animating a listing marker in the map.
     *
     * @since  1.2.0
     * @param  int                      listingId
     * @param  google.maps.Animation    animationType
     * @return void|null
     */
    window.animateListingMarker = function animateListingMarker( listingId, animationType ) {
        // Using default animation type.
        if ( ! animationType ) {
            animationType = google.maps.Animation.BOUNCE;
        }
        for ( var i = 0, max = markers.length; i < max; i++ ) {
            for ( var j = 0, maxListings = markers[ i ]['listing_id'].length; j < maxListings; j++ ) {
                if ( listingId == markers[ i ]['listing_id'][j] ) {
                    animatedMarker = markers[ i ];
                    markers[ i ].setAnimation( animationType );
                    return;
                }
            }
        }
    }

    /**
     * Stoping an animated marker in the map if exists any animated marker in the map.
     *
     * @since  1.2.0
     * @return void
     */
    window.stopAnimatedMarker = function stopAnimatedMarker() {
        if ( animatedMarker ) {
            animatedMarker.setAnimation( null );
        }
    }

    /**
     * Auto zoom feature of the map.
     * Auto zoom to showing all of listings in the map if possible.
     *
     * @since  1.2.0
     * @return void
     */
    window.mapAutoZoom = function() {
        var bounds = new google.maps.LatLngBounds();
        for ( i = 0, max = markers.length; i < max; i++ ) {
            bounds.extend( markers[i].getPosition() );
        }
        //center the map to the geometric center of all markers
        map.setCenter( bounds.getCenter() );
        map.fitBounds( bounds );

        // Don't get markers when auto zoom changes zoom level.
        get_markers = false;
        //remove one zoom level to ensure no marker is on the edge.
        map.setZoom( map.getZoom() - 1 >= 0 ? map.getZoom() - 1 : map.getZoom() );
        // set a minimum zoom
        // if you got only 1 marker or all markers are on the same address map will be zoomed too much.
        if ( map.getZoom() > 15 ) {
            // Don't get markers when auto zoom changes zoom level.
            get_markers = false;
            map.setZoom( 15 );
        }
    }

    /**
     * Generating an info bubble object type.
     *
     * @since 1.0.0
     * @return InfoBubble
     */
    window.generateInfoBubble = function generateInfoBubble() {
        return new InfoBubble({
                    minWidth: 320,
                    minHeight: 280,
                    hideCloseButton: true
                });
    }

    /**
     * Styling close button of InfoBubble.
     *
     * @since 1.0.0
     * @param string key
     * @param string value
     */
    InfoBubble.prototype.setCloseButtonStyle = function( key, value ) {
        this.close_.style[ key ] = value;
    };

})( jQuery, window );
