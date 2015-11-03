<?php
namespace Michaels\Manager\Bags;

use Michaels\Manager\Exceptions\BadFileInfoObjectException;

/**
 * Class FileBag - a collection object needed to create SplFileInfo objects from file references, in order to inject
 * into the FileLoader. This collection class can only store SplFileInfo objects. Anything else will cause exceptions.
 *
 * @package Michaels\Manager\Bags
 */
class FileBag
{
    /**
     * @var array An array of SplFileInfo objects
     */
    protected $fileObjects = [];

    /**
     * Constructs a new FileBag
     * @param $arrayOfSplFileInfoObjects
     */
    public function __construct($arrayOfSplFileInfoObjects)
    {
        $this->initialize($arrayOfSplFileInfoObjects);
    }

    /**
     * Return an array of SplFileInfo objects
     * @return array
     */
    public function getAllFileInfoObjects()
    {
        return $this->fileObjects;
    }

    /**
     * Reset the FileBag back to an empty array.
     * @return array - an empty array
     */
    public function emptyBag()
    {
        $this->fileObjects = [];
        return $this->fileObjects;
    }

    /**
     * Set up the bag with a proper array of SplFileInfo objects
     * @param array $splFileInfoObjects
     * @internal param $arrayOfSplFileInfoObjects
     */
    protected function initialize(array $splFileInfoObjects)
    {
        foreach ($splFileInfoObjects as $object) {
            if ($this->isSplFileInfoObject($object)) {
                array_push($this->fileObjects, $object);
            } else {
                throw new BadFileInfoObjectException('The input array does not hold proper SplFileInfo objects.');
            }
        }
    }

    /**
     * Check for an \SplFileInfo object
     * @param $object
     * @return bool
     */
    protected function isSplFileInfoObject($object)
    {
        return ($object instanceof \SplFileInfo);
    }
}