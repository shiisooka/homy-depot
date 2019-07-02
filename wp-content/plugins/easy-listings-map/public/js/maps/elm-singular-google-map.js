jQuery( function( $ ) {
    var map = null;

    function initialize_map() {
        // If map initialized already don't init again.
        if ( map ) {
            return;
        }

        var mapTypeIds = [];
        for ( i = 0, max = elm_singular_map.map_types.length; i < max; i++ ) {
            if ( 'ROADMAP' === elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.ROADMAP );
            } else if ( 'SATELLITE' ===  elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.SATELLITE );
            } else if ( 'HYBRID' === elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.HYBRID );
            } else if ( 'TERRAIN' === elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.TERRAIN );
            }
        }

        var latlng = new google.maps.LatLng( elm_singular_map.latitude, elm_singular_map.longitude );
        var map_options = {
            zoom: parseInt( elm_singular_map.zoom ),
            scrollwheel: false,
            center: latlng,
            mapTypeControl: true,
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
        if ( elm_singular_map.map_styles.length ) {
            map_options.styles = jQuery.parseJSON( elm_singular_map.map_styles );
        }
        map = new google.maps.Map( document.getElementById( elm_singular_map.map_id ), map_options);
        // Setting default map type if exists.
        if ( typeof elm_singular_map.default_map_type != 'undefined' ) {
          map.setMapTypeId( google.maps.MapTypeId[ elm_singular_map.default_map_type ] );
        }
        if ( elm_singular_map.latitude && elm_singular_map.longitude ) {
            var marker = new google.maps.Marker({
                position: latlng,
                map: map
            });
        }

        google.maps.event.addListener( map, 'tilesloaded', function() {
          jQuery('#gmap-loading').remove();
        });
    }

    if ( 'object' === typeof google && 'object' === typeof google.maps ) {
        $( function() {
            if ( $( '#' + elm_singular_map.map_id ).is( ':visible' ) ) {
                initialize_map();
            } else {
                // Map is inside bootstrap tabs so init map when it is visible.
                $( 'a[data-toggle="tab"]' ).on('shown shown.bs.tab', function ( e ) {
                    if ( $( '#' + elm_singular_map.map_id ).is( ':visible' ) ) {
                        initialize_map();
                    }
                });
                // Map is inside jQuery or zozoui tabs so init map when it is visible.
                $( 'ul.ui-tabs-nav li, ul.z-tabs-nav li' ).on( 'click', function( e ) {
                    if ( $( '#' + elm_singular_map.map_id ).is( ':visible' ) ) {
                        initialize_map();
                    }
                });
            }
        });
    }
    google.maps.event.addDomListener(window, 'resize', function() {
        if ( map ) {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        }
    });

});
