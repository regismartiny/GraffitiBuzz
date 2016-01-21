var map,
    map_info,
    map_lat,
    map_lng,
    clusters = [],
    pinImagePath = '/img/graffiti_pin.png',
    dataUrl = "/server/get_data.php";
//var site = "http://www.graffiti.buzz";
var site = "http://localhost/graffiti";


///////////////utility functions/////////////////////////////////////////////////////
function $(strId) {
    var type = strId.charAt(0);
    if (type == "#")
        return document.getElementById(strId.substring(1, strId.length));
    else
        return document.getElementsByTagName(strId);
}


function get(url, callback) {
    try {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState === 4) { // request is done
                if (httpRequest.status === 200) { // successfully
                    //console.log("Resposta: |" + httpRequest.responseText + "|");
                    callback(httpRequest.responseText);
                }
            }
        };
    } catch (e) {
        console.log("Error fetching data: " + e);
    }
    httpRequest.open('GET', url, true); //true = does not wait until complete
    httpRequest.send();
}
///////////////////////////////////////////////////////////////////////////////////////////





function initMap() {
    navigator.geolocation.getCurrentPosition($fn_posicao, $fn_erros);
}

// se houver erro ou a posição for negada, Feliz / RS
function $fn_erros() {
    map_lat = -29.4550665;
    map_lng = -51.3054748;
    $fn_maps();
}
// se der tudo certo, mapa baseado na localização
function $fn_posicao(e) {
    map_lat = e.coords.latitude;
    map_lng = e.coords.longitude;
    $fn_maps();
}
// detalhes do mapa
function $fn_maps() {
    var map_options = {
        // How zoomed in you want the map to start at (always required)
        zoom: 11,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(map_lat, map_lng),
        // How you would like to style the map. 
        // This is where you would paste any style found on Snazzy Maps.
        styles: [{
            "featureType": "landscape",
            "stylers": [{
                "hue": "#FFBB00"
            }, {
                "saturation": 43.400000000000006
            }, {
                "lightness": 37.599999999999994
            }, {
                "gamma": 1
            }]
        }, {
            "featureType": "road.highway",
            "stylers": [{
                "hue": "#FFC200"
            }, {
                "saturation": -61.8
            }, {
                "lightness": 45.599999999999994
            }, {
                "gamma": 1
            }]
        }, {
            "featureType": "road.arterial",
            "stylers": [{
                "hue": "#FF0300"
            }, {
                "saturation": -100
            }, {
                "lightness": 51.19999999999999
            }, {
                "gamma": 1
            }]
        }, {
            "featureType": "road.local",
            "stylers": [{
                "hue": "#FF0300"
            }, {
                "saturation": -100
            }, {
                "lightness": 52
            }, {
                "gamma": 1
            }]
        }, {
            "featureType": "water",
            "stylers": [{
                "hue": "#0078FF"
            }, {
                "saturation": -13.200000000000003
            }, {
                "lightness": 2.4000000000000057
            }, {
                "gamma": 1
            }]
        }, {
            "featureType": "poi",
            "stylers": [{
                "hue": "#00FF6A"
            }, {
                "saturation": -1.0989010989011234
            }, {
                "lightness": 11.200000000000017
            }, {
                "gamma": 1
            }]
        }]
    };

    map = new google.maps.Map($('#map-wrap'), map_options);
    map_info = new google.maps.InfoWindow({
        maxWidth: 400,
    });
    google.maps.event.addListener(map, 'idle', function() {
        var bounds = map.getBounds();
        // Call you server with ajax passing it the bounds
        // In the ajax callback delete the current markers and add new markers
        // With the new list of markers you can remove the current markers (marker.setMap(null)) 
        // that are on the map and add the new ones (marker.setMap(map)).
    });
    fetchMarkers();
}

function fetchMarkers() {
    var url = site + dataUrl + "?id=*" + "&info=3";
    get(url, addMarkers);
}

function addMarkers(data) {
    if (data != null) {

        var o = JSON.parse(data); //{ id: xx, gpsCoord: xx}

        if (o != null) {

            var image = {
                url: site + pinImagePath,
                size: new google.maps.Size(46, 70),
                scaledSize: new google.maps.Size(56, 90)
            };

            o.forEach(function(element) {

                var id = element.id;
                var gps = element.gpsCoord;
                console.log(element.id + ", " + element.gpsCoord);

                var geograficas_ = gps.split(",");
                var map_coords = new google.maps.LatLng(parseFloat(geograficas_[0]), parseFloat(geograficas_[1]));

                var marker = new google.maps.Marker({
                    map: map,
                    position: map_coords,
                    title: id,
                    animation: google.maps.Animation.DROP,
                    icon: image
                });
                // agrupar marcadores
                clusters.push(marker);
                // ao clicar no pin do mapa, exibir info
                google.maps.event.addListener(marker, 'click', function() {
                    map.setCenter(marker.getPosition());
                    var infowindow = "<div align=center>Id: " + id + "<br>GPS: " + gps + "<br><input type=image src=" + site + "/img/loading.png></div>";
                    map_info.setContent(infowindow);
                    map_info.open(map, marker);
                    fetchDetailedMarkerInfo(id);
                });
            });

        }
        var mcOptions = {
            gridSize: 25,
            maxZoom: 15
        };
        var markerCluster = new MarkerClusterer(map, clusters, mcOptions);
    }
}



function fetchDetailedMarkerInfo(id) {
    var url = site + dataUrl + "?id=" + id + "&info=1";
    get(url, setDetailedMarkerInfo);
}

function setDetailedMarkerInfo(data) {
    if (data != null) {
        var o = JSON.parse(data);
        if (o != null) {
            var infowindow = "<div align=center>Id: " + o.Id + "<br>Arquivo: " + o.FileName + "<br>GPS:" + o.GpsCoord + "<br>Artista:" + o.Artist + "<br>Tamanho:" + o.FileSize + "bytes" + "<br>Data Upload:" + o.UploadTime + "<br>Data Criação:" + o.CreationTime + "<br>Orientação:" + o.Orientation + "<br>Largura:" + o.ImageWidth + "<br>Altura:" + o.ImageHeight + "<br>Make:" + o.Make + "<br>Software:" + o.Software + "<br>Modelo:" + o.Model + "<br>Flash:" + o.Flash + "<br>SceneCaptureType:" + o.SceneCaptureType + "<br><a href=" + site + dataUrl + "?id=" + o.Id + "><input type=image src=data:image/jpg;base64," + o.Thumbnail + "></a></div>";
            map_info.setContent(infowindow);
        }
        $('#status').style.display = "inline";
    }
}



/*
// get data from html
var divindex, id, gpscoord, start, end;
try {
    var divs = $('div');
    var wanted = $('#dom-target');
    start = [].indexOf.call(wanted.parentElement.children, wanted);
    wanted = $('#dom-target-end');
    end = [].indexOf.call(wanted.parentElement.children, wanted) + start;
    //$('status').style.display = "inline";
    var infos = 2;
    for (var i = start; i < end; i += infos) {
        //divindex = i;
        id = divs[i + 1].textContent;
        gpsCoord = divs[i + 2].textContent;
        //alert("i:" + i + "\nid:" + id + "\ngps:" + gpsCoord);
        addMarker(id, gpsCoord);
    }
} catch (e) {
    $('#status').style.display = "inline";
    $('#errorLbl').innerHTML += e;
    //$('errorLbl').innerHTML += '<br>divindex:'+divindex+'<br>divslength:'+divslength+'<br>offset:'+offset+'<br>id:'+id+'<br>gpscoord:'+gpsCoord;
} */





/*
to find a dom element index
var K = -1;
for (var i = myNode.parent.childNodes.length; i >= 0; i--)
{
    if (myNode.parent.childNodes[i] === myNode)
    {
        K = i;
        break;
    }
}

if (K == -1)
    alert('Not found?!');

*/
