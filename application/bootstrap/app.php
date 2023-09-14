<?php
/* @var Concrete\Core\Application\Application $app */
/* @var Concrete\Core\Console\Application $console only set in CLI environment */

/*
 * ----------------------------------------------------------------------------
 * # Custom Application Handler
 *
 * You can do a lot of things in this file.
 *
 * ## Set a theme by route:
 *
 * $app->make('\Concrete\Core\Page\Theme\ThemeRouteCollection')
 * ->setThemeByRoute('/login', 'greek_yogurt');
 *
 *
 * ## Register a class override.
 *
 * $app->bind('helper/feed', function() {
 * 	 return new \Application\Core\CustomFeedHelper();
 * });
 *
 * $app->bind('\Concrete\Attribute\Boolean\Controller', function($app, $params) {
 * 	return new \Application\Attribute\Boolean\Controller($params[0]);
 * });
 *
 * ## Register Events.
 *
 * Events::addListener('on_page_view', function($event) {
 * 	$page = $event->getPageObject();
 * });
 *
 *
 * ## Register some custom MVC Routes
 *
 * Route::register('/test', function() {
 * 	print 'This is a contrived example.';
 * });
 *
 * Route::register('/custom/view', '\My\Custom\Controller::view');
 * Route::register('/custom/add', '\My\Custom\Controller::add');
 *
 * ## Pass some route parameters
 *
 * Route::register('/test/{foo}/{bar}', function($foo, $bar) {
 *  print 'Here is foo: ' . $foo . ' and bar: ' . $bar;
 * });
 *
 *
 * ## Override an Asset
 *
 * use \Concrete\Core\Asset\AssetList;
 * AssetList::getInstance()
 *     ->getAsset('javascript', 'jquery')
 *     ->setAssetURL('/path/to/new/jquery.js');
 *
 * or, override an asset by providing a newer version.
 *
 * use \Concrete\Core\Asset\AssetList;
 * use \Concrete\Core\Asset\Asset;
 * $al = AssetList::getInstance();
 * $al->register(
 *   'javascript', 'jquery', 'path/to/new/jquery.js',
 *   array('version' => '2.0', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false)
 *   );
 *
 * ----------------------------------------------------------------------------
 */

 use Concrete\Core\Application\Application;
 use Symfony\Component\HttpFoundation\JsonResponse;

 $router = $app->make('router');

//  $router->get('/api/current_user', function() {
//     return 'simple string.';
//  });

//  $router->get('/api/current_user', function() {
//     $u = new \User();
//     if ($u->isRegistered()) {
//         $data = [];
//         $data['user_id'] = $u->getUserID();
//         $data['username'] = $u->getUserName();
//         return new JsonResponse($data);
//     } else {
//         return new JsonResponse([], 400);
//     }
//  });

//global $sourceGltf;
//$sourceGltf = "gltfDemo\woolly-mammoth-100k-4096.gltf";
global $uID;
$uID = 123;
$shuffleSq = false;
$timeStampVal = 30;

global $UID_MAX_LENGTH;
global $USHF_MAX_LENGTH;
global $TIMESTAMP_MAX_LENGTH;
global $TIMESTAMP_VALIDITY_MAX_LENGTH;
global $SHFL_MATRIX_SIZE;

$UID_MAX_LENGTH = 10;
$USHF_MAX_LENGTH = 10;
$TIMESTAMP_MAX_LENGTH = 10;
$TIMESTAMP_VALIDITY_MAX_LENGTH = 5;
$SHFL_MATRIX_SIZE = 6 * 5;

//Create unique list of ten integer values from a list of values ranging from 0 to 29.
global $shuffleSq;
$shuffleSq = array_rand(range(0,29), $USHF_MAX_LENGTH);
shuffle($shuffleSq);
//this is for testing purposes to confirm that each element is a unique value.
// foreach($shuffleSq as $value) {
//     echo "$value <br>";
// }
$encoded;

//convert the BASE60 value to a unicode character
function num2B60($num) {
    if ($num > 59 or $num < 0) {
        echo "BASE60 encoder received number out of range: $num";
    }

    if ($num >= 52) {
        return mb_chr($num -52 + 50, 'UTF-8');
    } elseif ($num >= 26) {
        return mb_chr($num -26 + 97, "UTF-8");
    } else {
        return mb_chr($num + 65);
    }
}

function b602Num($uniChar) {
    $charList = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","2","3","4","5","6","7","8","9");
    if (in_array($uniChar, $charList)) {
        $num = mb_ord($uniChar, "UTF-8");
        if ($num >= 97) {
            return $num -97 + 26;
        } elseif ($num >= 65) {
            return $num -65;
        } else {
            return $num -50 + 52;
        }
    } else {
        echo "BASE60 decoder received symbol out of range.";
    }
}


//Generate Token
function encodeAndGenerateToken($sourceGltf, $uID, $ushf, $tsv) {
    global $UID_MAX_LENGTH;
    global $USHF_MAX_LENGTH;
    global $TIMESTAMP_MAX_LENGTH;
    global $TIMESTAMP_VALIDITY_MAX_LENGTH;
    global $SHFL_MATRIX_SIZE;

    $token = "";
    $currentTime = time();

    $headerLength = $UID_MAX_LENGTH + $USHF_MAX_LENGTH + $TIMESTAMP_MAX_LENGTH + $TIMESTAMP_VALIDITY_MAX_LENGTH;
    
    $idxs = array_rand(range(0,59), $headerLength);
    shuffle($idxs);

    for ($i = 0; $i < count($idxs); $i++) {
        $token .= num2B60($idxs[$i]);
    }
    for ($i = 0; $i < 61; $i++) {
        $token .= ".";
    }
    
    //echo "Token: $token <br>";
    
    // Write symbols representing UID decimals one by one into formerly randomly chosen positions.
    $tempUID = str_pad($uID, $UID_MAX_LENGTH, "0", STR_PAD_LEFT);
    //echo "Temp UID: $tempUID <br>";
    for ($i = 0; $i < $UID_MAX_LENGTH; $i++) {
        $newToken = substr($token, 0, $headerLength + $idxs[$i]); 
        $newToken .= num2B60(substr($tempUID, -1) + (rand(0, 5) * 10));
        $newToken .= substr($token, $headerLength + $idxs[$i] + 1); 
        $token = $newToken;
        //Don't understand this.
        $tempUID = substr($tempUID, 0, -1);
    }
    //echo "Temp UID: $tempUID <br>";
    //echo "<br>$token <br>";

    //Write symbols representing a unique Shuffling sequence for this particular user. 
    $idxOffSet = $UID_MAX_LENGTH;
    for ($i = 0; $i < $USHF_MAX_LENGTH; $i++) {
        $newToken = substr($token, 0, $headerLength + $idxs[$i + $idxOffSet]);
        $newToken .= num2B60($ushf[$USHF_MAX_LENGTH - $i - 1] + (rand(0, 1) * 30));
        $newToken .= substr($token, $headerLength + $idxs[$i + $idxOffSet] + 1);
        $token = $newToken;
    }
    //echo "Token: $token <br>";

    //Write symbols representing Timestamp decimals.
    $idxOffSet += $USHF_MAX_LENGTH;
    $thisMomentTemp = strval($currentTime);

    for ($i = 0; $i < $TIMESTAMP_MAX_LENGTH; $i++) {
        $newToken = substr($token, 0, $headerLength + $idxs[$i + $idxOffSet]);
        $newToken .= num2B60($thisMomentTemp[-1] + (rand(0, 5) * 10));
        $newToken .= substr($token, $headerLength + $idxs[$i + $idxOffSet] + 1);
        $token = $newToken;
        $thisMomentTemp = substr($thisMomentTemp, 0, -1);
    }
    //echo "This Moment Temp: $thisMomentTemp <br>";
    //echo "New Token: $newToken <br>";

    //Write symbols representing Timestamp validity decimals.
    $idxOffSet += $UID_MAX_LENGTH;
    $tsvTemp = str_pad($tsv, $TIMESTAMP_VALIDITY_MAX_LENGTH, 0, STR_PAD_LEFT);
    for ($i = 0; $i < $TIMESTAMP_VALIDITY_MAX_LENGTH; $i++) {
        $newToken = substr($token, 0, $headerLength + $idxs[$i + $idxOffSet]);
        $newToken .= num2B60(substr($tsvTemp, -1) + (rand(0, 5) * 10));
        $newToken .= substr($token, $headerLength + $idxs[$i + $idxOffSet] + 1);
        $token = $newToken;
        $tsvTemp = substr($tsvTemp, 0, -1);
    }
    //echo "Token: $token <br>";

    //Fill the remaining blank spaces with random-generated BASE60 symbols.
    for ($i = $headerLength; $i < strlen($token); $i++) {
        if($token[$i] == '.') {
            $newToken = substr($token, 0, $i);
            $newToken .= num2B60(rand(0, 59));
            $newToken .= substr($token, $i + 1);
            $token = $newToken;
        }
    }
    //echo "Token: $token <br>";

    //Empty shuffling matrix with 6 rows and 5 columns.
    $shMat = array(
        array(0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0)
    );

    //Update the source GLTF file by adding zeros to the count value in the accessors.
    
    //$sGltfContents = file_get_contents($sourceGltf);
    //$data = json_decode($sGltfContents, true);

    

    $gltfDup = json_decode($sourceGltf, true);
    $data = $gltfDup;

    // Fill buffer length with zeros
    $sEncKey = str_pad($data["accessors"][0]["count"], $USHF_MAX_LENGTH, 0, STR_PAD_LEFT);
    $data['accessors'][0]['count'] = $sEncKey;
    $updatedGltf = json_encode($data, JSON_PRETTY_PRINT);
    
    $gltfDup = $updatedGltf;
    //file_put_contents($gltfDup, $updatedGltf);
    //echo "<br>";
    //echo file_get_contents($sourceGltf);
    
    //Write hidden key digits into the shuffling matrix.
    $shOffsetList = array();
    for ($i = 0; $i < $USHF_MAX_LENGTH; $i++) {
        $shOffset = b602Num($token[$headerLength + b602Num($token[$i + $UID_MAX_LENGTH])]) % 30;
        array_push($shOffsetList, $shOffset);
        $shMat[floor($shOffset / 5)][$shOffset % 5] = intval($sEncKey[$i]);
    }
    
    //Fill unused spaces in shuffling matrix with random decimals.
    for ($i = 0; $i < $SHFL_MATRIX_SIZE; $i++) {
        if(!in_array($i, $shOffsetList)) {
            $shMat[floor($i / 5)][$i % 5] = rand(0,9);
        }
    }

    // for ($i = 0; $i < 6; $i++) {
    //     echo "<br>";
    //     for ($j = 0; $j < 5; $j++) {
    //         echo $shMat[$i][$j];
    //     }
    // }

    $egltf = $gltfDup;

    //$eGltfContents = file_get_contents($egltf);
    $eGltfContents = $egltf;
    $eGltfData = json_decode($eGltfContents, true);
    for ($i = 0; $i < 3; $i++) {
        $eGltfData['accessors'][$i]['count'] = 1;
    }

    //$sGltfContents = file_get_contents($gltfDup);
    $sGltfContents = $gltfDup;
    $sGltfData = json_decode($sGltfContents, true);
    //echo "<br> SourceGLTF: $sGltfContents <br>";
    for ($i = 0; $i < 3; $i++) {
        $sVal = strval($sGltfData['accessors'][0]['max'][$i]);
        $sEncVal = substr($sVal, 0, -6);
        for ($j = 0; $j < 5; $j++) {
            $sEncVal .= strval($shMat[$i][$j]);
        }
        $sEncVal .= substr($sVal, -1);
        //echo "string encrypted value: $sEncVal <br>";
        $eGltfData['accessors'][0]['max'][$i] = floatval($sEncVal);
    }

    for ($i = 0; $i < 3; $i++) {
        $sVal = strval($sGltfData['accessors'][0]['min'][$i]);
        $sEncVal = substr($sVal, 0, -6);
        for ($j = 0; $j < 5; $j++) {
            $sEncVal .= strval($shMat[$i + 3][$j]);
        }
        $sEncVal .= substr($sVal, -1);
        $eGltfData['accessors'][0]['min'][$i] = floatval($sEncVal);
    }

    $updatedEGltf = json_encode($eGltfData, JSON_PRETTY_PRINT);
    //file_put_contents($egltf, $updatedEGltf);
    $egltf = $updatedEGltf;
    //echo "<br>Encrypted GLTF:";
    //$egltfGet = file_get_contents($egltf);
    $egltfGet = $egltf;
    //echo $egltfGet;
    $egltfDec = json_decode($egltfGet);
    $egltfEnc = json_encode($egltfDec);

    $testArray = json_encode(array("value1" => $egltfGet, "value2" => $token));
    
    
    //return $egltfGet;
    //return $token;
    return $testArray;
    
}

 $router->get('/ccm/api/1.0/files/{fileID}', function($fileID) {
        $file = \File::getByID($fileID);

        // $file = \Concrete\Core\File\File::getByID($fileID);
        // $set = \Concrete\Core\File\Set\Set::createAndGetSet(
        //     'My File Set',
        //     \Concrete\Core\File\Set\Set::TYPE_PUBLIC
        // );
        // $set->addFileToSet($file);
        

        $resource = $file->getFileResource();
        $newFile = $resource->read();
        global $shuffleSq;

        $fileURL = $file->getURL();

        //return new JsonResponse($newFile);
        $fileExtension = strtolower(pathinfo($fileURL, PATHINFO_EXTENSION));

        if ($fileExtension === 'gltf') {
            return encodeAndGenerateToken($newFile, 477, $shuffleSq, 123);
        } else {
            //return new JsonResponse($resource);
            return $newFile;
        }
        
        
    
 });

 $router->get('/ccm/api/1.0/blocks/{blockID}', function($blockID) {
        $block = \Block::getByID($blockID);

        if ($block) {
            $data = [];
        $data['blockType'] = $block->getBlockTypeID();
        $data['blockID'] = $block->getBlockID();
        $data['controller'] = $block->getController();

        return new JsonResponse($data);
        } else {
            return new JsonResponse([], 400);
        }


 });
