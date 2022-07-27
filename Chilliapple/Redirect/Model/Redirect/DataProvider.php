<?php
namespace Chilliapple\Redirect\Model\Redirect;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Chilliapple\Redirect\Model\ResourceModel\Redirect\CollectionFactory as RedirectCollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * Loaded data cache
     *
     * @var array
     */
    protected $loadedData;

    /**
     * Data persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RedirectCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RedirectCollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Chilliapple\Redirect\Model\Redirect $redirect */
        foreach ($items as $redirect) {
            $this->loadedData[$redirect->getId()] = $redirect->getData();
            if (isset($this->loadedData[$redirect->getId()]['file'])) {
                $file = [];
                $file[0]['name'] = $redirect->getFile();
                $file[0]['url'] = $redirect->getFileUrl();
                $this->loadedData[$redirect->getId()]['file'] = $file;
            }
        }
        $data = $this->dataPersistor->get('chilliapple_redirect_redirect');
        if (!empty($data)) {
            $redirect = $this->collection->getNewEmptyItem();
            $redirect->setData($data);
            $this->loadedData[$redirect->getId()] = $redirect->getData();
            $this->dataPersistor->clear('chilliapple_redirect_redirect');
        }
        return $this->loadedData;
    }
}
