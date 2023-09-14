<?php
declare(strict_types=1);

namespace Application\Block\NewTest;

use Concrete\Core\Block\BlockController;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\File;
use Concrete\Core\File\Version;
use Concrete\Core\Http\Service\Json;
use Concrete\Core\File\StorageLocation\Configuration;
use Concrete\Core\Tree\Type\FileManager;

class Controller extends BlockController
{

    protected $btTable = "btNewTest";
    protected $btInterfaceWidth = "350";
    protected $btInterfaceHeight = "240";
    protected $btDefaultSet = 'basic';

    public function getBlockTypeName(): string
    {
        return t('New Test');
    }

    public function getBlockTypeDescription(): string
    {
        return t('A simple starting block for developers');
    }

    public function validate($args)
    {
        $error = parent::validate($args);
        if (!is_array($args)) {
            $error->add('Invalid data type, data must be an array.');
            return $error;
        }

        $field1 = $args['field1'] ?? null;
        if (!$field1) {
            $error->add(t('You must put something in the field 1 box.'));
        }

        return $error;
    }

    // public function save($args)
    // {
    //     $args['photoID'] = ($args['photoID'] != '') ? intval($args['photoID']) : 0;
    //     parent::save($args);
    // }

    public function save($args)
    {
        $args['gltfID'] = ($args['gltfID'] != '') ? intval($args['gltfID']) : 0;
        $args['binaryID'] = ($args['binaryID'] != '') ? intval($args['binaryID']) : 0;
        //load the file by file ID
        $gltfFileID = File::getByID($args['gltfID']);

        //check if the file was loaded
        if (is_object($gltfFileID)) {

            //get the path of the loaded file
            $modify = $gltfFileID->getVersionToModify();
            $gltfFile = $modify->getFile();
            $filePath = $gltfFile->getURL();

            //read the content of the file
            $gltfContent = file_get_contents($filePath);
            if (is_string($gltfContent)) {
                //decode the gltf file
                $data = json_decode($gltfContent, true);
            } else {
                throw new Exception('There is an error.');
            }

            if (isset($data['buffers'][0]['uri'])) {
                $originalURI = $data['buffers'][0]['uri'];
            } else {
                throw new Exception('Invalid');
            }

            //$data['buffers'][0]['uri'] = 'woolly-mammoth-100k-4096.bin';



            // $treeNode = \Concrete\Core\Tree\Node\Type\File::getTreeNodeFromTypeFile($gltfFile);
            // if ($treeNode instanceof \Concrete\Core\Tree\Node\Type\File) {
            //     $treeNodeID = $treeNode->getTreeNodeID();
            //     if ($treeNodeID != 66) {
            //         throw new Exception('Wrong Tree Node ID');
            //     }
            // }




            //change the uri
            $data['buffers'][0]['uri'] = $args['binaryID'] . '';
            //$data['buffers'][0]['uri'] = '99';

            // if ($originalURI === $data['buffers'][0]['uri']) {
            //     throw new Exception('Did not update.');
            // }

            //encode the updated data
            $updatedJson = json_encode($data, JSON_PRETTY_PRINT);

            $localFilePath = File::getRelativePathFromID($args['gltfID']);
            $trimmedPath = ltrim($localFilePath, '/');

            //put the updated data back to the directory
            file_put_contents($trimmedPath, $updatedJson);
        } else {
            throw new Exception('There is an error.');
        }

        parent::save($args);
    }

}