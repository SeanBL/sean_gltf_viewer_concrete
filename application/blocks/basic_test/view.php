<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div id="gltf-viewer-container"></div>
<head>
<!-- <script src="https://cdn.jsdelivr.net/gh/paulmasson/threejs-with-controls@r121/build/three.min.js"></script> -->
<!-- <script src="packages/my_package/threejs-with-controls-master/build/three.min.js"></script> -->

<!-- <script src="packages/my_package/gltf_viewer_clone-main/build/three.min.js"></script> -->

<!-- <script src="packages/my_package/hacked-gltf-viewer-clone-main/build/three.min.js"></script> -->

<!-- <script src="https://cdn.jsdelivr.net/npm/three-gltf-loader@1.111.0/index.min.js"></script> -->


<!-- <script src="packages/my_package/three.js-dev/build/three.js"></script>
<script src="packages/my_package/three.js-dev/examples/jsm/capabilities/WebGL"></script>
<script src="packages/my_package/OrbitControls.js"></script>
<script src="packages/my_package/three.js-dev/examples/jsm/loaders/GLTFLoader.js"></script> -->


<!-- <script src="packages/my_package/three.js-dev/examples/jsm/libs/stats.module.js"></script> -->

</head>
<script>
    
$(document).ready(function() {

const UID_MAX_LENGTH = 10;
const USHF_MAX_LENGTH = 10;
const TIMESTAMP_MAX_LENGTH = 10;
const TIMESTAMP_VALIDITY_MAX_LENGTH = 5;
const SHFL_MATRIX_SIZE = 6 * 5;

// let egltf = '{"accessors":[{"bufferView":0,"componentType":5126,"count":1,"max":[0.9607167317084,1.7219889198941,2.5408611968757],"min":[-0.9601930000524,-1.7222570020573,-2.5406730822498],"type":"VEC3"},{"bufferView":1,"componentType":5126,"count":1,"type":"VEC3"},{"bufferView":2,"componentType":5126,"count":1,"type":"VEC2"},{"bufferView":3,"componentType":5125,"count":300000,"type":"SCALAR"}],"asset":{"generator":"MeshSmith mesh conversion tool","version":"2.0"},"bufferViews":[{"buffer":0,"byteLength":883056,"target":34962},{"buffer":0,"byteLength":883056,"byteOffset":883056,"target":34962},{"buffer":0,"byteLength":588704,"byteOffset":1766112,"target":34962},{"buffer":0,"byteLength":1200000,"byteOffset":2354816,"target":34963}],"buffers":[{"byteLength":3554816,"uri":"woolly-mammoth-100k-4096.bin"}],"images":[{"uri":"woolly-mammoth-100k-4096-occlusion.jpg"},{"uri":"woolly-mammoth-100k-4096-normals.jpg"}],"materials":[{"name":"default","normalTexture":{"index":1},"occlusionTexture":{"index":0},"pbrMetallicRoughness":{"metallicFactor":0.100000001490116,"roughnessFactor":0.800000011920929}}],"meshes":[{"primitives":[{"attributes":{"NORMAL":1,"POSITION":0,"TEXCOORD_0":2},"indices":3,"material":0,"mode":4}]}],"nodes":[{"mesh":0}],"scene":0,"scenes":[{"nodes":[0]}],"textures":[{"source":0},{"source":1}]}';

// let egltfObj = JSON.parse(egltf);
// console.log(egltfObj);

const charList = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz23456789';
function b602num(asc) {
    if (charList.includes(asc)) {
        let num = asc.charCodeAt();
        if (num >= 97) {
            return num - 97 + 26;
        } else if (num >= 65) {
            return num - 65;
        } else {
            return num - 50 + 52;
        }
        
    } else {
        console.log("Base60 decoder received symbol out of range: " + asc);
    }
}



//let etkn = "EXpmuP2dS7UFKiGJzqbWlxQevtw4cCMTYL6JnLPH2vBxCyAhEFesYo2QZEHBTeqaKh8ORD9FPoBlOOJoseigroeKDxVKoBGb";

function decodeGltfAndToken(egltf, etkn) {
    const headerLength = UID_MAX_LENGTH + USHF_MAX_LENGTH + TIMESTAMP_MAX_LENGTH + TIMESTAMP_VALIDITY_MAX_LENGTH;
    let unshOffsetList = [];
    for (let i = 0; i < USHF_MAX_LENGTH; i++) {
        let unshOffset = b602num(etkn.charAt(headerLength + b602num(etkn.charAt(i + UID_MAX_LENGTH)))) % 30;
        unshOffsetList.push(unshOffset);
    }

    //Transcribe data from encoded GLTF into matrix form
    let unshMatrix = [[0,0,0,0,0], [0,0,0,0,0], [0,0,0,0,0], [0,0,0,0,0], [0,0,0,0,0], [0,0,0,0,0]];
    for (let i = 0; i < 3; i++) {
        let encValStr = String(egltf["accessors"][0]["max"][i]);
        let encVal = encValStr.slice(-6, -1);
        for (let j = 0; j < 5; j++) {
            unshMatrix[i][j] = encVal.charAt(j);
        }
    }

    for (let i = 0; i < 3; i++) {
        let encValStr = String(egltf["accessors"][0]["min"][i]);
        let encVal = encValStr.slice(-6, -1);
        for (let j = 0; j < 5; j++) {
            unshMatrix[i + 3][j] = encVal.charAt(j);
        }
    }

    for (let i = 0; i < 6; i++) {
        
        for (let j = 0; j < 5; j++) {
            console.log(unshMatrix[i][j]);
        }
    }

    //Extract matrix shuffling offsets from token and reconstruct the missing key value.
    let decKey = "";
    for (let i = 0; i < USHF_MAX_LENGTH; i++) {
        decKey += String(unshMatrix[Math.floor(unshOffsetList[i] / 5)][unshOffsetList[i] % 5]);
    }
    decKey = String(parseInt(decKey));
    console.log(decKey);
    
    // Decode user ID.
    let decUID = "";
    for (let i = 0; i < UID_MAX_LENGTH; i++) {
        let uidDec = String(b602num(etkn.charAt(headerLength + b602num(etkn.charAt(i)))) % 10);
        decUID += uidDec;
    }
    
    console.log(decUID);
    // Reverse the string function.
    function reverseString(str) {
        let splitString = str.split("");
        let reverseString = splitString.reverse();
        let joinString = reverseString.join("");
        return joinString;
    }
    let newDecUID = reverseString(decUID);
    console.log(newDecUID);
    newDecUID = String(parseInt(newDecUID));
    console.log(newDecUID);

    // Decode UNIX timestamp.
    let decTstp = '';
    for (let i = 0; i < TIMESTAMP_MAX_LENGTH; i++) {
        let tstpDec = String(b602num(etkn.charAt(headerLength + b602num(etkn.charAt(i + UID_MAX_LENGTH + USHF_MAX_LENGTH)))) % 10);
        decTstp += tstpDec;
    }
    let newDecTstp = reverseString(decTstp);
    newDecTstp = String(parseInt(newDecTstp));
    console.log(newDecTstp);

    // Decode UNIX timestamp validity interval.
    let decTstpVal = '';
    for (let i = 0; i < TIMESTAMP_VALIDITY_MAX_LENGTH; i++) {
        let tstpValDec = String(b602num(etkn.charAt(headerLength + b602num(etkn.charAt(i + UID_MAX_LENGTH + USHF_MAX_LENGTH + TIMESTAMP_MAX_LENGTH)))) % 10);
        decTstpVal += tstpValDec;
    }
    let newDecTstpVal = reverseString(decTstpVal);
    newDecTstpVal = String(parseInt(newDecTstpVal));
    console.log(newDecTstpVal);

    let decGltf = egltf;
    decGltf["accessors"][0]["count"] = parseInt(decKey);
    decGltf["accessors"][1]["count"] = parseInt(decKey);
    decGltf["accessors"][2]["count"] = parseInt(decKey);

    console.log(decGltf["accessors"][0]["count"]);
    console.log(decGltf["accessors"][1]["count"]);
    console.log(decGltf["accessors"][2]["count"]);
    
    console.log(decGltf);

    return decGltf;
    
}


        const xhr = new XMLHttpRequest();
        xhr.open("GET", "http://exrxtest/index.php/ccm/api/1.0/files/44");
        xhr.send();
        //xhr.responseType = "JSON";
        xhr.onload = () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.response);
                let parsedArray = JSON.parse(xhr.response);
                let token = parsedArray.value2;
                let eGltf = JSON.parse(parsedArray.value1);
                console.log(eGltf);
                console.log(parsedArray.value1);
                console.log(token);
                let gltf = decodeGltfAndToken(eGltf, token);
                
                console.log(gltf);
                
                display(gltf);
            } else {
                console.log(`error: ${xhr.status}`);
            }
        };

        // Create a new GLTFLoader instance
        const loader = new THREE.GLTFLoader();

        // Path to your GLTF file
        //const gltfFilePath = 'packages/my_package/spoiled_gltf/woolly-mammoth-100k-4096.gltf';
        //const gltfFilePath = 'packages/my_package/mammoth/woolly-mammoth-100k-4096.gltf';

        //const gltfFilePath = 'http://exrxtest/index.php/ccm/api/1.0/files/40';
        // let test = JSON.parse(gltfFilePath);
        // console.log(test);
        
        //const gltfFilePath = 'packages/my_package/car/scene.gltf';

        //Animation files
        //const gltfFilePath = 'packages/my_package/fish_animation_swiming/scene.gltf';
        //const gltfFilePath = 'packages/my_package/dragon_animation_flying/scene.gltf';

        // Create a scene, camera, and renderer
        const scene = new THREE.Scene();
        
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('gltf-viewer-container').appendChild(renderer.domElement);

        // Add lights or other scene elements as needed
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        ambientLight.castShadow = true;
        scene.add(ambientLight);

        const spotLight = new THREE.SpotLight(0xffffff, 1);
        spotLight.castShadow = true;
        spotLight.position.set(0, 64, 32);
        scene.add(spotLight);

        let mixer;

        function display(gltfFilePath) {
        //Load the GLTF model
        loader.parse(gltfFilePath, 'index.php/ccm/api/1.0/files/', function(gltf) {
            
            const animations = gltf.animations;
            scene.add(gltf.scene);
            mixer = new THREE.AnimationMixer(gltf.scene);

            animations.forEach((animation) => {
                mixer.clipAction(animation).play();
            })
            
        }, function(xhr) {
            console.log((xhr.loaded / xhr.total * 100) + '% loaded');
        }, function(error) {
            console.error(error);
        }); 
        
        camera.position.z = 5;

        // Control the view
        const controls = new THREE.OrbitControls(camera, renderer.domElement);

        // Add stats for performance monitoring
        // var stats = new Stats();
        // document.body.appendChild(stats.dom);

        // const geometry = new THREE.BoxGeometry( 3, 3, 3 );
        // const material = new THREE.MeshNormalMaterial();
        // const cube = new THREE.Mesh( geometry, material);
        // scene.add( cube );

        const clock = new THREE.Clock();

        const params = {
            color: '#000000'
        }
        scene.background = new THREE.Color(params.color);

        // const gui = new datgui();
        // gui.addColor(params, 'color').onChange(function(value) {
        //     scene.background.set(value);
        // });

        function animate() {
            requestAnimationFrame(animate);

            if (mixer) {
                const delta = clock.getDelta();
                mixer.update(delta);
            }

            //stats.update();
            controls.update();

            renderer.render(scene, camera);
        }
        return animate();
        }
        // Check if WebGL is available and start the animation
        // if (THREE.WEBGL.isWebGLAvailable()) {
        //     animate();
        // } else {
        //     var warning = THREE.WEBGL.getWebGLErrorMessage();
        //     document.getElementById('gltf-viewer-container').appendChild(warning);
        // }
        //testing
    });

</script>
<footer>
    <script src="packages/my_package/gltf_viewer_clone-main/build/three.min.js"></script>
    <!-- <script src="application/blocks/basic_test/dat.gui-master/build/dat.gui.min.js"></script> -->
</footer>

