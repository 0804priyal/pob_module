<?php

namespace Chilliapple\Login\Plugin;

use Magento\Customer\Model;

class AccountManagement
{
    const FIELD_PASSWORD = 'ee_password';

    const FIELD_PASSWORD_SALT = 'ee_salt';

    private $hashAlgos = array(
      128   => 'sha512',
      64    => 'sha256',
      40    => 'sha1',
      32    => 'md5'
    );
    /**
     * @var Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var Helper\Data
     */
    private $encryptionHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @param Model\CustomerRegistry $customerRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(Model\CustomerRegistry $customerRegistry,
                                \Psr\Log\LoggerInterface $logger, 
                                \Magento\Framework\App\ResourceConnection $resource)
    {
      $this->customerRegistry = $customerRegistry;
      $this->logger = $logger;
      $this->resource = $resource;
    }

    /**
     * @param $subject
     * @param $username
     * @param $password
     */
    public function beforeAuthenticate($subject, $username, $password)
    {
      try {
        
        $customer = $this->customerRegistry->retrieveByEmail($username);
        $hashPassword =  $customer->getPasswordHash();
        $hasPassword =  $this->hasOldPassword($customer->getId());

        if ($hasPassword) {
                $hashPassword =  $customer->getPasswordHash();
                $byteSize = strlen($hashPassword);
                //$salt = $customer->getOrigData(self::FIELD_PASSWORD_SALT);
                $salt = $this->getSalt($customer->getId());
                $hashedPair = $this->hashPassword($password, $salt, $byteSize);
          if ($hashPassword == $hashedPair['password'])
           {
            //avoiding customer required fields check
            $this->updateCustomerPassword($customer->getId(), $customer->hashPassword($password));
            $this->deleteTempAttributes($customer->getId());
            //force take password hash from database in \Magento\Customer\Model\AccountManagement
            $this->customerRegistry->remove($customer->getId());
          }
        }
      } catch (\ReflectionException $e) {
        $this->logger->notice($e->getMessage());
      } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        //no such customer
      } catch (\Exception $e) {
        $this->logger->error($e);
      }
    }

    /**
     * @param $customerId
     * @param $password
     */
    private function updateCustomerPassword($customerId, $password)
    {
      $customerEntityTable = $this->resource->getTableName('customer_entity');
      $this->getConnection()->update($customerEntityTable, ['password_hash' => $password], "entity_id = $customerId");
    }

    /**
     * @param $customerId
     */
    private function deleteTempAttributes($customerId)
    {
      //TODO refactor direct query
      $customerEntityVarcharTable = $this->resource->getTableName('customer_entity_varchar');
      $eavAttributeTable = $this->resource->getTableName('eav_attribute');

      $this->getConnection()->query("
        DELETE cev
          FROM {$customerEntityVarcharTable} cev
        INNER JOIN {$eavAttributeTable} ea
          ON cev.attribute_id = ea.attribute_id
        WHERE
          cev.entity_id = {$customerId}
          AND (ea.attribute_code = " . $this->getConnection()->quote(AccountManagement::FIELD_PASSWORD) . "
          OR ea.attribute_code = " . $this->getConnection()->quote(AccountManagement::FIELD_PASSWORD_SALT) . ")");
    }

    private function getSalt($customerId)
    {
      //TODO refactor direct query
      $customerEntityVarcharTable = $this->resource->getTableName('customer_entity_varchar');
      $eavAttributeTable = $this->resource->getTableName('eav_attribute');
      $bind = ['customer_id' => $customerId, 'attribute_code' => AccountManagement::FIELD_PASSWORD_SALT];
      $select = $this->getConnection()->select()->from(
                ['cev' => $customerEntityVarcharTable],
                ['value']
            )
            ->join(['ea' => $eavAttributeTable],'cev.attribute_id = ea.attribute_id')
            ->where(
                'cev.entity_id = :customer_id'
            )
            ->where(
              'ea.attribute_code = :attribute_code'
            );
      $salt = $this->getConnection()->fetchOne($select, $bind);
      return $salt;
    }

    private function hasOldPassword($customerId)
    {
      //TODO refactor direct query
      $customerEntityVarcharTable = $this->resource->getTableName('customer_entity_varchar');
      $eavAttributeTable = $this->resource->getTableName('eav_attribute');
      $bind = ['customer_id' => $customerId, 'attribute_code' => AccountManagement::FIELD_PASSWORD];
      $select = $this->getConnection()->select()->from(
                ['cev' => $customerEntityVarcharTable],
                ['value']
            )
            ->join(['ea' => $eavAttributeTable],'cev.attribute_id = ea.attribute_id')
            ->where(
                'cev.entity_id = :customer_id'
            )
            ->where(
              'ea.attribute_code = :attribute_code'
            );
      $hasPassword = $this->getConnection()->fetchOne($select, $bind);
      return $hasPassword;
    }
  /**
   * @return \Magento\Framework\DB\Adapter\AdapterInterface
   */
    private function getConnection()
    {
      if (!$this->connection) {
        $this->connection = $this->resource->getConnection('core_write');
      }
      return $this->connection;
    }

    public function hashPassword($password, $salt = FALSE, $byteSize = FALSE)
    {
      // No hash function specified? Use the best one
      // we have access to in this environment.
      if ($byteSize === FALSE)
      {
        reset($this->hashAlgos);
        $byteSize = key($this->hashAlgos);
      }
      elseif ( ! isset($this->hashAlgos[$byteSize]))
      {
         return false;
      }

      // No salt? (not even blank), we'll regenerate
      if ($salt === FALSE)
      {
        $salt = '';

        // The salt should never be displayed, so any
        // visible ascii character is fair game.
        for ($i = 0; $i < $byteSize; $i++)
        {
          $salt .= chr(mt_rand(33, 126));
        }
      }
      elseif (strlen($salt) !== $byteSize)
      {
        // they passed us a salt that isn't the right length,
        // this can happen if old code resets a new password
        // ignore it
        $salt = '';
      }

      return array(
        'salt'    => $salt,
        'password'  => hash($this->hashAlgos[$byteSize], $salt.$password)
      );
    }

}
