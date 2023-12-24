<?php


namespace Orchestra\bandwidth\storage;

interface Storage
{
    
    /**
     * Returns the Mutex for this storage.
     *
     * @return Mutex The mutex.
     * @internal
     */
    public function getMutex();
    
    /**
     * Returns if the storage was already bootstrapped.
     *
     * @return bool True if the storage was already bootstrapped.
     * @throws StorageException Checking the state of the storage failed.
     * @internal
     */
    public function isBootstrapped();
    
    /**
     * Bootstraps the storage.
     *
     * @param double $microtime The timestamp.
     * @throws StorageException Bootstrapping failed.
     * @internal
     */
    public function bootstrap($microtime);
    
    /**
     * Removes the storage.
     *
     * After a storage was removed you should not use that object anymore.
     * The only defined methods after that operations are isBootstrapped()
     * and bootstrap(). A call to bootstrap() results in a defined object
     * again.
     *
     * @throws StorageException Cleaning failed.
     * @internal
     */
    public function remove();
    
    /**
     * Stores a timestamp.
     *
     * @param double $microtime The timestamp.
     * @throws StorageException Writing to the storage failed.
     * @internal
     */
    public function setMicrotime($microtime);

    /**
     * Indicates, that there won't be any change within this transaction.
     *
     * @internal
     */
    public function letMicrotimeUnchanged();

    /**
     * Returns the stored timestamp.
     *
     * @return double The timestamp.
     * @throws StorageException Reading from the storage failed.
     * @internal
     */
    public function getMicrotime();
}