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
    //    print_r([$userId,$cryptocurrency,$amount]);die();
        $query= $this->database->prepare('INSERT INTO user_cryptocurrencies values(:userId,:cryptocurrencyId,:amount)');
        $query->bindParam(':userId', $userId, Database::PARAM_INT);
        $query->bindParam(':cryptocurrencyId', $cryptocurrency->getId(), Database::PARAM_STR);
        $query->bindParam(':amount', $amount, Database::PARAM_INT);

        $query->execute();
    }

    public function subtractCryptocurrencyFromUser(int $userId, Cryptocurrency $cryptocurrency, int $amount): void
    { 
        // TODO
    
         $query= $this->database->prepare('SELECT amount,cryptocurrency_id FROM user_cryptocurrencies WHERE user_Id=:userId');
         $query->bindParam(':userId', $userId, Database::PARAM_INT);
         $query->execute();
        $result = $query->fetchAll();
        $cryptocurrencyId=  $cryptocurrency->getId();
        foreach($result as $row){
            if($amount > $row['amount']){
                print_r('Invalid Amount');die();
            }
           elseif ($amount <= $row['amount']) {
               # code...
                 if($cryptocurrency->getId() == $row['cryptocurrency_id']  ){
                $oldAmount=$row['amount'];
                $NewAmount=$oldAmount-$amount;
                $sql = $this->database->prepare(' UPDATE user_cryptocurrencies SET amount=:amount WHERE user_Id=:userId AND cryptocurrency_id = :cryptocurrencyId' );
                $sql->bindParam(':userId', $userId, Database::PARAM_INT);
                $sql->bindParam(':cryptocurrencyId', $cryptocurrencyId, Database::PARAM_STR);
                $sql->bindParam(':amount', $NewAmount, Database::PARAM_INT);
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
