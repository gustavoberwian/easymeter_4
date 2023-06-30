const canvas = document.getElementById('canvas');

const width = canvas.width;
const height = canvas.height;

// Colors
const black = new THREE.Color('black');
const white = new THREE.Color('white');

function loadFile(filename) {
    return new Promise((resolve, reject) => {
        const loader = new THREE.FileLoader();

        loader.load(filename, (data) => {
            resolve(data);
        });
    });
}

// Shader chunks
loadFile('/vendor/threejs-water-master/shaders/utils.glsl').then((utils) => {
    THREE.ShaderChunk['utils'] = utils;

    // Create Renderer
    const camera = new THREE.PerspectiveCamera(75, width / height, 0.01, 100);
    camera.position.set(0.426, 0.677, -2.095);
    camera.rotation.set(2.828, 0.191, 3.108);

    const renderer = new THREE.WebGLRenderer({canvas: canvas, antialias: true, alpha: true});
    renderer.setSize(width, height);
    renderer.autoClear = false;

    // Light direction
    const light = [0.7559289460184544, 0.7559289460184544, -0.3779644730092272];

    // Create mouse Controls
    const controls = new THREE.TrackballControls(
        camera,
        canvas
    );

    controls.screen.width = width;
    controls.screen.height = height;

    controls.rotateSpeed = 2.5;
    controls.zoomSpeed = 1.2;
    controls.panSpeed = 0.9;
    controls.dynamicDampingFactor = 0.9;

    // Ray caster
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    const targetgeometry = new THREE.CylinderGeometry(2, 2);
    for (let vertex of targetgeometry.vertices) {
        vertex.z = -vertex.y;
        vertex.y = 0.;
    }
    const targetmesh = new THREE.Mesh(targetgeometry);

    // Textures
    const cubetextureloader = new THREE.CubeTextureLoader();

    const textureCube = cubetextureloader.load([
        '/vendor/threejs-water-master/xpos.jpg', '/vendor/threejs-water-master/xneg.jpg',
        '/vendor/threejs-water-master/ypos.jpg', '/vendor/threejs-water-master/ypos.jpg',
        '/vendor/threejs-water-master/zpos.jpg', '/vendor/threejs-water-master/zneg.jpg',
    ]);

    const textureloader = new THREE.TextureLoader();

    const tiles = textureloader.load('/vendor/threejs-water-master/tiles.jpg');

    class WaterSimulation {

        constructor() {
            this._camera = new THREE.OrthographicCamera(0, 1, 1, 0, 0, 2000);

            const radius = 1; // Raio da piscina
            const segments = 15; // Número de segmentos da geometria
            this._geometry = new THREE.CircleBufferGeometry(radius, segments);


            this._textureA = new THREE.WebGLRenderTarget(256, 256, {type: THREE.FloatType});
            this._textureB = new THREE.WebGLRenderTarget(256, 256, {type: THREE.FloatType});
            this.texture = this._textureA;

            const shadersPromises = [
                loadFile('/vendor/threejs-water-master/shaders/simulation/vertex.glsl'),
                loadFile('/vendor/threejs-water-master/shaders/simulation/drop_fragment.glsl'),
                loadFile('/vendor/threejs-water-master/shaders/simulation/normal_fragment.glsl'),
                loadFile('/vendor/threejs-water-master/shaders/simulation/update_fragment.glsl'),
            ];

            this.loaded = Promise.all(shadersPromises)
                .then(([vertexShader, dropFragmentShader, normalFragmentShader, updateFragmentShader]) => {
                    const dropMaterial = new THREE.RawShaderMaterial({
                        uniforms: {
                            center: {value: [0, 0]},
                            radius: {value: 0},
                            strength: {value: 0},
                            texture: {value: null},
                        },
                        vertexShader: vertexShader,
                        fragmentShader: dropFragmentShader,
                    });

                    const normalMaterial = new THREE.RawShaderMaterial({
                        uniforms: {
                            delta: {value: [1 / 256, 1 / 256]},  // TODO: Remove this useless uniform and hardcode it in shaders?
                            texture: {value: null},
                        },
                        vertexShader: vertexShader,
                        fragmentShader: normalFragmentShader,
                    });

                    const updateMaterial = new THREE.RawShaderMaterial({
                        uniforms: {
                            delta: {value: [1 / 256, 1 / 256]},  // TODO: Remove this useless uniform and hardcode it in shaders?
                            texture: {value: null},
                        },
                        vertexShader: vertexShader,
                        fragmentShader: updateFragmentShader,
                    });

                    this._dropMesh = new THREE.Mesh(this._geometry, dropMaterial);
                    this._normalMesh = new THREE.Mesh(this._geometry, normalMaterial);
                    this._updateMesh = new THREE.Mesh(this._geometry, updateMaterial);
                });
        }

        // Add a drop of water at the (x, y) coordinate (in the range [-1, 1])
        addDrop(renderer, x, y, radius, strength) {
            this._dropMesh.material.uniforms['center'].value = [x, y];
            this._dropMesh.material.uniforms['radius'].value = radius;
            this._dropMesh.material.uniforms['strength'].value = strength;

            this._render(renderer, this._dropMesh);
        }

        stepSimulation(renderer) {
            this._render(renderer, this._updateMesh);
        }

        updateNormals(renderer) {
            this._render(renderer, this._normalMesh);
        }

        _render(renderer, mesh) {
            // Swap textures
            const oldTexture = this.texture;
            const newTexture = this.texture === this._textureA ? this._textureB : this._textureA;

            mesh.material.uniforms['texture'].value = oldTexture.texture;

            renderer.setRenderTarget(newTexture);

            // TODO Camera is useless here, what should be done?
            renderer.render(mesh, this._camera);

            this.texture = newTexture;
        }

    }


    class Caustics {
        constructor(lightFrontGeometry) {
            this._camera = new THREE.OrthographicCamera(0, 1, 1, 0, 0, 2000);

            const radius = 1; // Raio da piscina circular
            const segments = 15; // Número de segmentos da geometria
            this._geometry = new THREE.CircleBufferGeometry(radius, segments);

            this.texture = new THREE.WebGLRenderTarget(1024, 1024, { type: THREE.UNSIGNED_BYTE });

            const shadersPromises = [
                loadFile('/vendor/threejs-water-master/shaders/caustics/vertex.glsl'),
                loadFile('/vendor/threejs-water-master/shaders/caustics/fragment.glsl')
            ];

            this.loaded = Promise.all(shadersPromises).then(([vertexShader, fragmentShader]) => {
                const material = new THREE.RawShaderMaterial({
                    uniforms: {
                        light: { value: light },
                        water: { value: null }
                    },
                    vertexShader: vertexShader,
                    fragmentShader: fragmentShader
                });

                this._causticMesh = new THREE.Mesh(this._geometry, material);
            });
        }

        update(renderer, waterTexture) {
            this._causticMesh.material.uniforms['water'].value = waterTexture;

            renderer.setRenderTarget(this.texture);
            renderer.setClearColor(black, 0);
            renderer.clear();

            // TODO Camera is useless here, what should be done?
            renderer.render(this._causticMesh, this._camera);
        }
    }



    class Water {
        constructor() {
            //this.geometry = new THREE.PlaneBufferGeometry(2, 2, 200, 200);
            this.geometry = new THREE.CircleBufferGeometry(1, 360);

            const shadersPromises = [
                loadFile('/vendor/threejs-water-master/shaders/water/vertex.glsl'),
                loadFile('/vendor/threejs-water-master/shaders/water/fragment.glsl')
            ];

            this.loaded = Promise.all(shadersPromises).then(([vertexShader, fragmentShader]) => {
                this.material = new THREE.RawShaderMaterial({
                    uniforms: {
                        light: { value: light },
                        tiles: { value: tiles },
                        sky: { value: textureCube },
                        water: { value: null },
                        causticTex: { value: null },
                        underwater: { value: false }
                    },
                    vertexShader: vertexShader,
                    fragmentShader: fragmentShader
                });

                this.mesh = new THREE.Mesh(this.geometry, this.material);
            });
        }

        draw(renderer, waterTexture, causticsTexture) {
            this.material.uniforms['water'].value = waterTexture;
            this.material.uniforms['causticTex'].value = causticsTexture;

            this.material.side = THREE.FrontSide;
            this.material.uniforms['underwater'].value = true;
            renderer.render(this.mesh, camera);

            this.material.side = THREE.BackSide;
            this.material.uniforms['underwater'].value = false;
            renderer.render(this.mesh, camera);
        }
    }



    class Pool {
        constructor() {
            this.geometry = new THREE.CylinderGeometry(1, 1, 2);
            const radius = 1; // Raio da piscina em forma redonda

            // Gerar vértices e índices para uma piscina redonda
            const vertices = [];
            const indices = [];
            const numSegments = 360; // Número de segmentos para a piscina redonda

            // Gerar os vértices em forma de círculo
            for (let i = 0; i <= numSegments; i++) {
                const theta = (i / numSegments) * Math.PI * 2;
                const x = Math.cos(theta) * radius;
                const y = 0; // Altura da piscina em forma redonda
                const z = Math.sin(theta) * radius;

                vertices.push(x, y, z);
            }

            // Gerar os índices para desenhar a piscina redonda como um triângulo fan
            for (let i = 1; i <= numSegments; i++) {
                indices.push(0, i, i + 1);
            }

            //this.geometry.setAttribute('position', new THREE.Float32BufferAttribute(vertices, 3));
            //this.geometry.setIndex(indices);

            const shadersPromises = [
                loadFile('/vendor/threejs-water-master/shaders/pool/vertex.glsl'),
                loadFile('/vendor/threejs-water-master/shaders/pool/fragment.glsl')
            ];

            this.loaded = Promise.all(shadersPromises).then(([vertexShader, fragmentShader]) => {
                this.material = new THREE.RawShaderMaterial({
                    uniforms: {
                        light: { value: light },
                        tiles: { value: tiles },
                        waterTexture: { value: null },
                        causticsTexture: { value: null }
                    },
                    vertexShader: vertexShader,
                    fragmentShader: fragmentShader
                });
                this.material.side = THREE.FrontSide;

                this.mesh = new THREE.Mesh(this.geometry, this.material);
            });
        }

        draw(renderer, waterTexture, causticsTexture) {
            this.material.uniforms['waterTexture'].value = waterTexture;
            this.material.uniforms['causticsTexture'].value = causticsTexture;

            renderer.render(this.mesh, camera);
        }
    }




    class Debug {

        constructor() {
            this._camera = new THREE.OrthographicCamera(0, 1, 1, 0, 0, 1);
            this._geometry = new THREE.PlaneBufferGeometry();

            const shadersPromises = [
                loadFile('/vendor/threejs-water-master/shaders/debug/vertex.glsl'),
                loadFile('/vendor/threejs-water-master/shaders/debug/fragment.glsl')
            ];

            this.loaded = Promise.all(shadersPromises)
                .then(([vertexShader, fragmentShader]) => {
                    this._material = new THREE.RawShaderMaterial({
                        uniforms: {
                            texture: {value: null},
                        },
                        vertexShader: vertexShader,
                        fragmentShader: fragmentShader,
                    });

                    this._mesh = new THREE.Mesh(this._geometry, this._material);
                });
        }

        draw(renderer, texture) {
            this._material.uniforms['texture'].value = texture;

            renderer.setRenderTarget(null);
            renderer.render(this._mesh, this._camera);
        }

    }

    const waterSimulation = new WaterSimulation();
    const water = new Water();
    const caustics = new Caustics(water.geometry);
    const pool = new Pool();

    const debug = new Debug();


    // Main rendering loop
    function animate() {
        waterSimulation.stepSimulation(renderer);
        waterSimulation.updateNormals(renderer);

        const waterTexture = waterSimulation.texture.texture;

        caustics.update(renderer, waterTexture);

        const causticsTexture = caustics.texture.texture;

        // debug.draw(renderer, causticsTexture);

        renderer.setRenderTarget(null);
        renderer.setClearColor(white, 1);
        renderer.clear();

        water.draw(renderer, waterTexture, causticsTexture);
        pool.draw(renderer, waterTexture, causticsTexture);

        controls.update();

        window.requestAnimationFrame(animate);
    }

    function onMouseMove(event) {
        const rect = canvas.getBoundingClientRect();

        mouse.x = (event.clientX - rect.left) * 2 / width - 1;
        mouse.y = -(event.clientY - rect.top) * 2 / height + 1;

        raycaster.setFromCamera(mouse, camera);

        const intersects = raycaster.intersectObject(targetmesh);

        for (let intersect of intersects) {
            waterSimulation.addDrop(renderer, intersect.point.x, intersect.point.z, 0.03, 0.04);
        }
    }

    const loaded = [waterSimulation.loaded, caustics.loaded, water.loaded, pool.loaded, debug.loaded];

    Promise.all(loaded).then(() => {
        canvas.addEventListener('mousemove', {handleEvent: onMouseMove});

        for (var i = 0; i < 20; i++) {
            waterSimulation.addDrop(
                renderer,
                Math.random() * 2 - 1, Math.random() * 2 - 1,
                0.03, (i & 1) ? 0.02 : -0.02
            );
        }

        animate();
    });

});
