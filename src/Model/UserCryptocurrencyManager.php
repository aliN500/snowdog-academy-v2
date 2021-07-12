<?php

namespace Snowdog\Academy\Model;

use Snowdog\Academy\Core\Database;

class UserCryptocurrencyManager
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getCryptocurrenciesByUserId(int $userId): array
    {
        $query = $this->database->prepare('SELECT c.id, c.name, uc.amount FROM user_cryptocurrencies AS uc LEFT JOIN cryptocurrencies AS c ON uc.cryptocurrency_id = c.id WHERE uc.user_id = :user_id');
        $query->bindParam(':user_id', $userId, Database::PARAM_INT);
        $query->execute();

        return $query->fetchAll(Database::FETCH_CLASS, UserCryptocurrency::class);
    }

    public function addCryptocurrencyToUser(int $userId, Cryptocurrency $cryptocurrency, int $amount): void
    {
        // TODO
  
       $cryptocurrencyId=$cryptocurrency->getId();
        $getData= $this->database->prepare('SELECT c.id, c.name,c.price,u.login,u.funds,uc.amount FROM user_cryptocurrencies AS uc JOIN cryptocurrencies AS c ON uc.cryptocurrency_id = c.id JOIN users AS u ON uc.user_id= u.id WHERE uc.user_id = :user_id');
        $getData->bindParam(':user_id', $userId, Database::PARAM_INT); 
        $getData->execute();
        $result=$getData->fetchAll();
        
      
      $getPrices=$this->database->prepare('SELECT name,price FROM cryptocurrencies Where id= :cryptocurrencyId ');
      $getPrices->bindParam(':cryptocurrencyId', $cryptocurrencyId, Database::PARAM_STR);
      $getPrices->execute();  
      $CoinPrice=$getPrices->fetchAll();

        if($amount > 0){
            foreach ($result as $r) {
                # code...
                $totalPrice=$CoinPrice[0][1]*$amount;
                $funds=$r['funds'];
               
            }
            
            if ($totalPrice>$funds) {
                print_r("Insufficent Funds");die();
            } else {
                # code...
                $CheckRecord=$this->database->prepare('SELECT cryptocurrency_id,amount FROM user_cryptocurrencies LEFT JOIN users ON users.id=user_cryptocurrencies.user_id Where cryptocurrency_id= :cryptocurrencyId AND user_id = :userId ');
                $CheckRecord->bindParam(':userId', $userId, Database::PARAM_INT);
                $CheckRecord->bindParam(':cryptocurrencyId', $cryptocurrencyId, Database::PARAM_STR);
                $CheckRecord->execute();  
                $check=$CheckRecord->fetchAll();
                
               
                $count=count($check);
                if($count==0){
                    $query= $this->database->prepare('INSERT INTO user_cryptocurrencies values(:userId,:cryptocurrencyId,:amount)');
                    $query->bindParam(':userId', $userId, Database::PARAM_INT);
                    $query->bindParam(':cryptocurrencyId', $cryptocurrencyId, Database::PARAM_STR);
                    $query->bindParam(':amount', $amount, Database::PARAM_INT);
            
                    $query->execute();
                    $remainFunds=$funds-$totalPrice;
                   
                    $sql = $this->database->prepare(' UPDATE users SET funds=:funds WHERE id=:userId' );
                    $sql->bindParam(':userId', $userId, Database::PARAM_INT);
                    $sql->bindParam(':funds', $remainFunds, Database::PARAM_INT);
                    $sql->execute();
                    
                   
                } else {
                    # code...
               
                    $remainFunds=$funds-$totalPrice;
                    $UpdateAmount=$check[0][1]+$amount;
           
                    $sql = $this->database->prepare(' UPDATE users,user_cryptocurrencies SET funds=:funds , amount=:amount  WHERE id=:userId AND user_cryptocurrencies.cryptocurrency_id=:cryptocurrencyId'  );
                    $sql->bindParam(':funds', $remainFunds, Database::PARAM_INT);
                    $sql->bindParam(':amount', $UpdateAmount, Database::PARAM_INT);
                    $sql->bindParam(':userId', $userId, Database::PARAM_INT);
                    $sql->bindParam(':cryptocurrencyId', $cryptocurrencyId, Database::PARAM_STR);

                    
                    $sql->execute();

                }
            }
            
          
        
        }
        else
            {
                print_r("Amount Should be Greater than 0");die();
            }
        
        
      
    }

    public function subtractCryptocurrencyFromUser(int $userId, Cryptocurrency $cryptocurrency, int $amount): void
    { 
        // TODO
        $cryptocurrencyId=$cryptocurrency->getId();
         $query= $this->database->prepare('SELECT amount,cryptocurrency_id,funds,price FROM user_cryptocurrencies LEFT JOIN users ON users.id=user_cryptocurrencies.user_id LEFT JOIN cryptocurrencies ON cryptocurrencies.id=user_cryptocurrencies.cryptocurrency_id WHERE user_Id=:userId AND cryptocurrency_id= :cryptocurrencyId');
         $query->bindParam(':userId', $userId, Database::PARAM_INT);
         $query->bindParam(':cryptocurrencyId', $cryptocurrencyId, Database::PARAM_STR);

         $query->execute();
        $result = $query->fetchAll();
        foreach ($result as $r) {
            # code...
            $NewAmount= $r[0]-$amount;
            $Price= $r[3]*$amount; 
            $funds=$r[2]+$Price;
          
            // print_r([$NewAmount,$Price,$funds]);die();
        }
      
        // $NewAmount= $result[0][0]-$amount;
        // $Price= $result[0][3]*$amount; 
        // $funds=$result[0][2]+$Price;
        // print_r($funds);die();
        $cryptocurrencyId=  $cryptocurrency->getId();
        foreach($result as $row){
            if($amount > $row['amount']){
                print_r('Invalid Amount');die();
            }
           elseif ($amount <= $row['amount']) {
               # code...
                 if($cryptocurrency->getId() == $row['cryptocurrency_id']  ){
                // $oldAmount=$row['amount'];
                // $NewAmount=$oldAmount-$amount;
                // $sql = $this->database->prepare(' UPDATE user_cryptocurrencies SET amount=:amount WHERE user_Id=:userId AND cryptocurrency_id = :cryptocurrencyId' );
                $sql = $this->database->prepare(' UPDATE users,user_cryptocurrencies SET funds=:funds , amount=:amount  WHERE id=:userId AND user_cryptocurrencies.cryptocurrency_id=:cryptocurrencyId'  );
                $sql->bindParam(':funds', $funds, Database::PARAM_INT);    
                $sql->bindParam(':amount', $NewAmount, Database::PARAM_INT);
                $sql->bindParam(':userId', $userId, Database::PARAM_INT);
                $sql->bindParam(':cryptocurrencyId', $cryptocurrencyId, Database::PARAM_STR);
                $sql->execute();

            }
        }
            

     }
     

     }

    public function getUserCryptocurrency(int $userId, string $cryptocurrencyId): ?UserCryptocurrency
    {
        $query = $this->database->prepare('SELECT * FROM user_cryptocurrencies WHERE user_id = :user_id AND cryptocurrency_id = :cryptocurrency_id');
        $query->bindParam(':user_id', $userId, Database::PARAM_INT);
        $query->bindParam(':cryptocurrency_id', $cryptocurrencyId, Database::PARAM_STR);
        $query->execute();

        /** @var UserCryptocurrency $result */
        $result = $query->fetchObject(UserCryptocurrency::class);

        return $result ?: null;
    }
}
