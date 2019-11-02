var wputh_map = function($map, settings) {
    'use strict';
    var self = this,
        _current_infowindow = false;

    self.init = function($map, settings) {
        self.create($map, settings);
    };

    self.create = function($map, settings) {
        var map = new google.maps.Map($map, {
            zoom: settings.zoom,
            center: settings.center
        });
        var _markers = [];
        for (var i = 0, len = settings.markers.length; i < len; i++) {
            _markers[i] = self.add_marker(map, settings.markers[i], settings.icon);
        }
    };

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

    self.init($map, settings);
    return self;
};
