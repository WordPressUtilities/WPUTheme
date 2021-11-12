var wputh_map = function($map, settings) {
    'use strict';
    var self = this,
        _markers = [],
        _map = false,
        _current_infowindow = false;

    self.init = function($map, settings) {
        self.create($map, settings);
    };

    self.create = function($map, settings) {
        _map = new google.maps.Map($map, settings);

        /* Add markers */
        self.add_markers(_map, settings);

        /* Close Infowindow on map click */
        google.maps.event.addListener(_map, "click", function() {
            if (_current_infowindow) {
                _current_infowindow.close();
            }
        });
    };

    self.getMap = function() {
        return _map;
    };

    self.set = function(id, val) {
        _map.set(id, val);
    };

    /* MARKERS
    -------------------------- */

    self.add_marker = function(map, marker_info, marker_icon) {
        var marker = {},
            marker_obj = {
                map: map,
                position: {
                    lat: marker_info.lat,
                    lng: marker_info.lng
                }
            };

        if (marker_icon) {
            marker_obj.icon = marker_icon;
        }
        marker.marker = new google.maps.Marker(marker_obj);

        if (marker_info.content) {
            marker.infowindow = new google.maps.InfoWindow({
                content: marker_info.content
            });

            marker.marker.addListener('click', function() {
                if (_current_infowindow) {
                    _current_infowindow.close();
                }

                document.dispatchEvent(new CustomEvent('wputh_map_marker_click', {
                    'detail': {
                        'map': map,
                        'marker': marker,
                    }
                }));

                marker.infowindow.open(map, marker.marker);
                _current_infowindow = marker.infowindow;
            });
        }

        return marker;
    };

    /* ADD */
    self.add_markers = function(map, settings) {
        if (!map) {
            map = _map;
        }
        _markers = [];
        for (var i = 0, len = settings.markers.length; i < len; i++) {
            if (!settings.markers[i]) {
                continue;
            }
            _markers[i] = self.add_marker(map, settings.markers[i], settings.icon);
        }
    };

    /* CLEAR */
    self.clear_markers = function() {
        for (var i = 0, len = _markers.length; i < len; i++) {
            _markers[i].marker.setMap(null);
        }
        _markers = [];
    };

    /* GET */
    self.get_markers = function() {
        return _markers;
    };

    /* Trigger marker open */
    self.open_marker = function(i) {
        new google.maps.event.trigger(_markers[i].marker, 'click');
    };

    /* INIT
    -------------------------- */

    self.init($map, settings);
    return self;
};
