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
   
       if(count($res)==1){
           $res[0]['ids'];
          
           $ch = curl_init("https://api.coingecko.com/api/v3/simple/price?ids=".$res[0]['ids']."&vs_currencies=USD");
          
           $fp = fopen("UpdatedPrices.json", "w");
        //    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

           curl_setopt($ch, CURLOPT_FILE, $fp);
           curl_setopt($ch, CURLOPT_HEADER, 0);
           $result=curl_exec($ch);

           if(curl_error($ch)) {
               fwrite($fp, curl_error($ch));
           
            }
           
           curl_close($ch);
           fclose($fp);
          
       }
      
        // $this->cryptocurrencyManager->updatePrice();
    }
}
