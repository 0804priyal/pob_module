<?php
namespace Chilliapple\Redirect\Api\Data;

/**
 * @api
 */
interface RedirectInterface
{
    const REDIRECT_ID = 'redirect_id';
    const SOURCE_URL = 'source_url';
    const DEST_URL = 'dest_url';
    const CODE = 'code';
    const FILE = 'file';
    /**
     * @param int $id
     * @return RedirectInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return RedirectInterface
     */
    public function setRedirectId($id);

    /**
     * @return int
     */
    public function getRedirectId();

    /**
     * @param string $sourceUrl
     * @return RedirectInterface
     */
    public function setSourceUrl($sourceUrl);

    /**
     * @return string
     */
    public function getSourceUrl();
    /**
     * @param string $destUrl
     * @return RedirectInterface
     */
    public function setDestUrl($destUrl);

    /**
     * @return string
     */
    public function getDestUrl();
    /**
     * @param string $code
     * @return RedirectInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();
    /**
     * @param string $file
     * @return RedirectInterface
     */
    public function setFile($file);

    /**
     * @return string
     */
    public function getFile();

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getFileUrl();
}
