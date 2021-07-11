<?php

namespace Snowdog\Academy\Controller;

use Snowdog\Academy\Model\Cryptocurrency;
use Snowdog\Academy\Model\CryptocurrencyManager;
use Snowdog\Academy\Model\UserCryptocurrencyManager;
use Snowdog\Academy\Model\UserManager;
use Snowdog\Academy\Model\User;

class Cryptos
{
    private CryptocurrencyManager $cryptocurrencyManager;
    private UserCryptocurrencyManager $userCryptocurrencyManager;
    private UserManager $userManager;
    private Cryptocurrency $cryptocurrency;

    public function __construct(
        CryptocurrencyManager $cryptocurrencyManager,
        UserCryptocurrencyManager $userCryptocurrencyManager,
        UserManager $userManager
    ) {
        $this->cryptocurrencyManager = $cryptocurrencyManager;
        $this->userCryptocurrencyManager = $userCryptocurrencyManager;
        $this->userManager = $userManager;
    }

    public function index(): void
    {
        require __DIR__ . '/../view/cryptos/index.phtml';
    }

    public function buy(string $id): void
    {
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        if (!$user) {
            header('Location: /cryptos');
            return;
        }

        $cryptocurrency = $this->cryptocurrencyManager->getCryptocurrencyById($id);
        if (!$cryptocurrency) {
            header('Location: /cryptos');
            return;
        }

        $this->cryptocurrency = $cryptocurrency;
        require __DIR__ . '/../view/cryptos/buy.phtml';
    }

    public function buyPost(string $id): void
    {
        extract($_POST);
        // TODO
        // verify if user is logged in
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        if (!$user) {
            header('Location: /account');
            return;
        }

        $userId = $user->getId();
        is_int($userId);
        $userAmount = $amount;
        if (is_int($userId) && is_int($amount)) {
            header('Location: /account');
            return;
        }
        $crypto = new Cryptocurrency();
        $crypto->setId($id);

        // use $this->userCryptocurrencyManager->addCryptocurrencyToUser() method
        $cryptoCurrencyBuy = $this->userCryptocurrencyManager->addCryptocurrencyToUser($userId, $crypto, $userAmount);
        header('Location: /cryptos');
    }

    public function sell(string $id): void
    {
       
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        if (!$user) {
            header('Location: /account');
            return;
        }

        $cryptocurrency = $this->cryptocurrencyManager->getCryptocurrencyById($id);
        if (!$cryptocurrency) {
            header('Location: /account');
            return;
        }

        $this->cryptocurrency = $cryptocurrency;
        require __DIR__ . '/../view/cryptos/sell.phtml';
    }

    public function sellPost(string $id): void
    {
        extract($_POST);
        // TODO
        // verify if user is logged in
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        if (!$user) {
            header('Location: /account');
            return;
        }
        $userId = $user->getId();
        is_int($userId);
        $userAmount = $amount;
        $crypto = new Cryptocurrency();
        $crypto->setId($id);
        // use $this->userCryptocurrencyManager->subtractCryptocurrencyFromUser() method
        $cryptocurrencySell=$this->userCryptocurrencyManager->subtractCryptocurrencyFromUser($userId, $crypto, $userAmount);
        
        header('Location: /cryptos');
    }

    public function getCryptocurrencies(): array
    {
        return $this->cryptocurrencyManager->getAllCryptocurrencies();
    }
}
