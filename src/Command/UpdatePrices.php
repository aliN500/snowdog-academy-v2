<?php

namespace Snowdog\Academy\Command;

use Exception;
use Snowdog\Academy\Core\Migration;
use Snowdog\Academy\Model\CryptocurrencyManager;
use Symfony\Component\Console\Output\OutputInterface;
use Snowdog\Academy\Core\Database;


class UpdatePrices
{
    private Database $database;

    private CryptocurrencyManager $cryptocurrencyManager;

    public function __construct(CryptocurrencyManager $cryptocurrencyManager,Database $database)
    {
        $this->cryptocurrencyManager = $cryptocurrencyManager;
        $this->database = $database;

    }

    public function __invoke(OutputInterface $output)
    {
        // TODO
        // use $this->cryptocurrencyManager->updatePrice() method
        $query = $this->database->query("SELECT GROUP_CONCAT(id SEPARATOR ',') as ids  FROM cryptocurrencies ");
        $query->execute();
        $res=$query->fetchAll();
        $url = "https://api.coingecko.com/api/v3/simple/price?ids=".$res[0]['ids']."&vs_currencies=USD";
      

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_json = curl_exec($ch);
        curl_close($ch);
        $response=json_decode($response_json, true);
    //    print_r($response);die();
        $response;  
        $new_array = array();  //<--- This is the new array you're building
        
        foreach($response as $i=>$element)
        {
            foreach($element as $j=>$sub_element)
            {
                $new_array[$j][$i] = $sub_element; //We are basically inverting the indexes
            }
        }
        $AllPrices=$new_array['usd'];            
                foreach( $AllPrices as $key => $value ){
                    
                    $sql = $this->database->prepare(' UPDATE cryptocurrencies SET price=:price  WHERE id=:cryptocurrencyId'  );
                    $sql->bindParam(':price', $value, Database::PARAM_INT);    
                    $sql->bindParam(':cryptocurrencyId', $key, Database::PARAM_STR);
                    $sql->execute();
                }

                  
           
     
   

      
        // $this->cryptocurrencyManager->updatePrice();
    }
}
