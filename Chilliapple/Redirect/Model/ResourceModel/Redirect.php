<?php
namespace Chilliapple\Redirect\Model\ResourceModel;

class Redirect extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('chilliapple_redirect_redirect', 'redirect_id');
    }


    public function deleteByOldSourceUrlIfExists($data)
    {
    	$sourceUrls = $data['source_url'];
    	$deleteIds = $this->getExistingRedirectIds([$sourceUrls]);

        if(count($deleteIds)){
    		$adapter = $this->getConnection();
            $where = [
                'redirect_id IN (?)' => $deleteIds
            ];
            $adapter->delete(
                $this->getMainTable(),
                $where
            );
        }
        return $this;
    }

    public function getExistingRedirectIds($sourceUrls)
    {
        $tbl = $this->getMainTable();

        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($tbl, 'redirect_id')
            ->where('source_url IN (?)', $sourceUrls);
        return $adapter->fetchCol($select);
    }

    public function uploadCsvData($csv)
    {
        $tbl = $this->getMainTable();

        $adapter = $this->getConnection();

        $sourceUrls = [];
        foreach($csv as $item){

        	if(!empty(trim($item['source_url'])) && !empty(trim($item['dest_url']))){
        		
	            $sourceUrls[] = trim($item['source_url']);
        	}
        }

        $deleteIds = $this->getExistingRedirectIds($sourceUrls);
        $insertData = null;

        //remove old on the basis of source_url
        if(count($deleteIds)){
            $where = [
                'redirect_id IN (?)' => $deleteIds
            ];
            $adapter->delete(
                $tbl,
                $where
            );
        }

        //insert new
        $count = 0;
        foreach($csv as $item){

        	if(!empty(trim($item['source_url'])) && !empty(trim($item['dest_url']))){

	            $insertRow = []; 
	            $insertRow['source_url'] = trim($item['source_url']);
	            $insertRow['dest_url'] 	 = trim($item['dest_url']);
	            $insertRow['code'] 		 = trim($item['code']);
	            $insertData[] = $insertRow;
	            $count++;
        	}
        }

        if($insertData){

            $adapter->insertOnDuplicate($tbl, $insertData);
        }

        return $count;
    }
}
