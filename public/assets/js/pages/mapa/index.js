(function () {

    "use strict";

    var center = [-74.2420436, 40.5456883]; // nova york
    //var center = [13.415118329414781, 52.53001062340084]; // nova york

    var map = new maptalks.Map("map", {
        center: center,
        zoom: 19,
        pitch: 52,
        doubleClickZoom: false,
        baseLayer: new maptalks.TileLayer('base', {
            urlTemplate: 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            subdomains: ['a', 'b', 'c']
        })
    });

// features to draw
    var features = [];

    var buildings = {};

    $.ajax({
        url: '/mapa/get_buildings',
        type: 'GET',
        dataType: 'json',
        async: false,
        success: function (data) {
            buildings = data;
        },
        error: function (xhr, status, error) {
            console.error('Erro na requisição AJAX: ' + error);
        }
    });

    buildings.forEach(function (b) {
        features = features.concat(b.features);
    });

// the ThreeLayer to draw buildings
    var threeLayer = new maptalks.ThreeLayer('t', {
        forceRenderOnMoving: true,
        forceRenderOnRotating: true,
        // animation: true
    });

    var meshs = [];
    threeLayer.prepareToDraw = function (gl, scene, camera) {
        var light = new THREE.DirectionalLight(0xffffff);
        light.position.set(0, -10, 10).normalize();
        scene.add(light);
        scene.add(new THREE.AmbientLight('#fff', 0.2));

        features.forEach(function (g) {
            var material = new THREE.MeshPhongMaterial({ color: g.properties.color,  });
            var highlightmaterial = new THREE.MeshBasicMaterial({ color: g.properties.colorHover });
            var mesh = threeLayer.toExtrudePolygon(maptalks.GeoJSON.toGeometry(g), {
                height: g.properties.height,
                asynchronous: true,
                altitude: g.properties.altitude
            }, material);

            //tooltip test
            mesh.setToolTip(g.properties.height, {
                showTimeout: 0,
                eventsPropagation: true,
                dx: 10
            });

            //infowindow test
            mesh.setInfoWindow({
                content: 'Nome do Reservatorio, Nivel:' + g.properties.height,
                title: 'Detalhes',
                animationDuration: 0,
                autoOpenOn: false
            });

            // mesh.getInfoWindow().addTo(map);

            //event test
            ['click', 'mousemove', 'mouseout', 'mouseover', 'mousedown', 'mouseup', 'dblclick', 'contextmenu'].forEach(function (eventType) {
                mesh.on(eventType, function (e) {
                    if (e.type === 'mouseout') {
                        this.setSymbol(material);
                    }
                    if (e.type === 'mouseover') {
                        this.setSymbol(highlightmaterial);
                    }
                });
            });
            meshs.push(mesh);
        });
        threeLayer.addMesh(meshs);
        //animateShow();
        threeLayer.config('animation', true);
    };
    const sceneConfig = {
        postProcess: {
            enable: true,
            antialias: {enable: true}
        }
    };
    const groupLayer = new maptalks.GroupGLLayer('group', [threeLayer], {sceneConfig});
    groupLayer.addTo(map);

    function animateShow() {
        meshs.forEach(function (mesh) {
            mesh.animateShow({
                duration: 3500
            });
        });
    }

}.apply(this, [jQuery]));