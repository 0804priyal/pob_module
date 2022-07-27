<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Api\Data;

/**
 * @api
 */
interface GovernmentInterface
{
    public const GOVERNMENT_ID = 'government_id';
    public const TITLE = 'title';
    public const FEATURE_IMAGE = 'feature_image';
    public const DESCRIPTION = 'description';
    public const GOVERNMENT_LINK = 'government_link';
    public const STORE_ID = 'store_id';
    public const META_TITLE = 'meta_title';
    public const META_DESCRIPTION = 'meta_description';
    public const META_KEYWORDS = 'meta_keywords';
    public const IS_ACTIVE = 'is_active';
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    /**
     * @param int $id
     * @return GovernmentInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return GovernmentInterface
     */
    public function setGovernmentId($id);

    /**
     * @return int
     */
    public function getGovernmentId();
    /**
     * @param string $title
     * @return GovernmentInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $featureImage
     * @return GovernmentInterface
     */
    public function setFeatureImage($featureImage);

    /**
     * @return string
     */
    public function getFeatureImage();

    /**
     * @param string $description
     * @return GovernmentInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $governmentLink
     * @return GovernmentInterface
     */
    public function setGovernmentLink($governmentLink);

    /**
     * @return string
     */
    public function getGovernmentLink();

    /**
     * @param int[] $store
     * @return GovernmentInterface
     */
    public function setStoreId($store);

    /**
     * @return int[]
     */
    public function getStoreId();

    /**
     * @param string $metaTitle
     * @return GovernmentInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @param string $metaDescription
     * @return GovernmentInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param string $metaKeywords
     * @return GovernmentInterface
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * @return string
     */
    public function getMetaKeywords();

    /**
     * @param int $isActive
     * @return GovernmentInterface
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getIsActive();
}
