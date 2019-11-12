var wputh_map = function($map, settings) {
    'use strict';
    var self = this,
        _markers = [],
        _current_infowindow = false;

    self.init = function($map, settings) {
        self.create($map, settings);
    };

    self.create = function($map, settings) {
        var map = new google.maps.Map($map, settings);
        self.add_markers(map, settings);
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
                marker.infowindow.open(map, marker.marker);
                _current_infowindow = marker.infowindow;
            });
        }

        return marker;
    };

    /* ADD */
    self.add_markers = function(map, settings) {
        _markers = [];
        for (var i = 0, len = settings.markers.length; i < len; i++) {
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

    /* INIT
    -------------------------- */

    self.init($map, settings);
    return self;
};
