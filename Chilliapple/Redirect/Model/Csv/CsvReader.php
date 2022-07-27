<?php


namespace Chilliapple\Redirect\Model\Csv;

class CsvReader 
{	
	protected $csv;

	protected $data;

	public function  __construct(
        \Magento\Framework\File\Csv $csv
	){
		$this->csv = $csv;
	}

	public function read($filepath)
	{
	   if (!is_file($filepath)){ 
	       throw new \Magento\Framework\Exception\LocalizedException(__('%1 file not found.',$filepath));
	   }

	   $this->data = $this->csv->getData($filepath);
	}

  public function readCsv($file){

    $this->data = $this->csv->getData($file);
    return $this;
  }

	public function getData()
	{
		return $this->data;
	}

	public function getHeaders()
	{
		if(isset($this->data[0]))
		{
			return $this->data[0];
		}
	}

    public function getRows($keepHeader = false, $combineHeader = true){

		    $rows = $this->data;

        if(!$keepHeader)
		    array_shift($rows);

        if(count($rows))
        { 
          $header = $this->getHeaders();
          $data = [];

          foreach($rows as $row){
            $data[] =  $this->adjustHeader($header, $row, $combineHeader);
            
          }
         
        }
        else
        {
          throw new \Magento\Framework\Exception\LocalizedException(__('Invalid row count'));
        }

        return $data;
    }

    public function adjustHeader($header, &$row, $combineHeader = false)
    {
      if(count($header)>count($row))
      {
        for ($i=(count($row)-1) ;$i<count($header) ; $i++) {
          $row[$i] = '';
        }
      }

      if($combineHeader){

      	return array_combine($header, $row);
      }

      return $row;
    }

}
